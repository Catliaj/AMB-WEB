<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsersModel;
use App\Models\ChatSessionModel;
use App\Models\MessageModel;
use App\Models\PropertyModel;
use App\Models\UserTokenModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\BookingModel;
use App\Models\PropertyImageModel;
use Dompdf\Dompdf;
use setasign\Fpdi\Fpdi;



class UserController extends BaseController
{
    /**
     * OTP request endpoint for signup
     */
    public function requestOtp()
    {
        $email = $this->request->getPost('Email');

        if (!$email) {
            return redirect()->back()->with('error', 'Email is required to send OTP.');
        }

        $otp = rand(100000, 999999);
        $otpExpiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        session()->set([
            'otp_email' => $email,
            'otp_code' => $otp,
            'otp_expiry' => $otpExpiry
        ]);


        $emailService = \Config\Services::email();
        $fromEmail = config('Email')->fromEmail ?? 'no-reply@example.com';
        $fromName = config('Email')->fromName ?? 'Your App';

        $emailService->setFrom($fromEmail, $fromName);
        $emailService->setTo($email);
        $emailService->setSubject('Your OTP Code');
        $emailService->setMessage("Your OTP code is: $otp\nThis code will expire in 10 minutes.");

        if ($emailService->send()) {
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'OTP sent to your email. Please check your inbox.'
        ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to send OTP. Please try again.'
            ]);
        }
    }

    /**
     * Return the user's age and birthdate as JSON
     * GET /users/getAge/{id}
     */
    public function getAge($id = null)
    {
        $session = session();
        // allow access only to logged-in users (or restrict further if needed)
        if (!$session->get('isLoggedIn')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        if (empty($id) || !is_numeric($id)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid user id']);
        }

        $usersModel = new UsersModel();
        $user = $usersModel->find($id);
        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'User not found']);
        }

        $birth = $user['Birthdate'] ?? $user['birthdate'] ?? null;
        if (empty($birth)) {
            return $this->response->setStatusCode(200)->setJSON(['age' => null, 'birthdate' => null]);
        }

        try {
            $dob = new \DateTime($birth);
            $now = new \DateTime();
            $age = $now->diff($dob)->y;
            return $this->response->setJSON(['age' => $age, 'birthdate' => $dob->format('Y-m-d')]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(200)->setJSON(['age' => null, 'birthdate' => $birth]);
        }
    }

    /**
     * Registration with email verification token
     */
    public function StoreUsers()
    {
        $session = session();
        $usersModel = new \App\Models\UsersModel();
        $tokenModel = new \App\Models\UserTokenModel();

        $email = $this->request->getPost('Email');
        $otp_code = $this->request->getPost('otp_code');


        $session_otp = $session->get('otp_code');
        $session_email = $session->get('otp_email');
        $session_expiry = $session->get('otp_expiry');

        if (!$session_otp || !$session_email || !$session_expiry) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Please request an OTP first.']);
        }
        if ($email !== $session_email) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Email does not match the OTP email.']);
        }
        if ($otp_code != $session_otp) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid OTP code.']);
        }
        if (strtotime($session_expiry) < time()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'OTP code has expired.']);
        }


        $data = [
            'FirstName'   => $this->request->getPost('FirstName'),
            'MiddleName'  => $this->request->getPost('MiddleName'),
            'LastName'    => $this->request->getPost('LastName'),
            'Birthdate'   => $this->request->getPost('Birthdate'),
            'phoneNumber' => $this->request->getPost('phoneNumber'),
            'Email'       => $email,
            'Password'    => password_hash($this->request->getPost('Password'), PASSWORD_BCRYPT),
            'Role'        => 'Client',
            'status'      => 'Online',
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        if ($usersModel->insert($data)) {
            $userID = $usersModel->getInsertID();


            $tokenModel->insert([
                'userID'     => $userID,
                'token'      => $otp_code, 
                'expires_at' => $session_expiry,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

 
            $session->remove(['otp_code', 'otp_email', 'otp_expiry']);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'User registered successfully',
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to register user.']);
    }

    /**
     * Login logic
     */
    public function login()
    {
        $session = session();
        $model = new UsersModel();

        $Username = $this->request->getPost('inputEmail');
        $Password = $this->request->getPost('inputPassword');

        $user = $model->where('Email', $Username)->first();

        if ($user && password_verify($Password, $user['Password'])) {

            // Check if email is verified
            if (isset($user['verified']) && !$user['verified']) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Please verify your email before logging in.'
                ]);
            }

            // Set session
            $session->set([
                'isLoggedIn'  => true,
                'UserID'      => $user['UserID'],
                'inputEmail'  => $user['Email'],
                'role'        => $user['Role'],
                'FirstName'   => $user['FirstName'],
                'MiddleName'  => $user['MiddleName'],
                'LastName'    => $user['LastName']
            ]);

            // Update online status
            $model->setOfflineToOnline($user['UserID']);

            // Determine redirect URL
            switch ($user['Role']) {
                case 'Agent':
                    $redirectURL = base_url('/users/agentHomepage');
                    break;
                case 'Admin':
                    $redirectURL = base_url('/admin/adminHomepage');
                    break;
                default:
                    $redirectURL = base_url('/users/clientHomepage');
                    break;
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Login successful!',
                'redirect' => $redirectURL
            ]);
        }

        // Invalid login
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Wrong username or password.'
        ]);
    }


    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }

    /**
     * Client Homepage
     */
    public function clientHomepage()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/'); // not logged in
        }

         if ($session->get('role') !== 'Client') {
            return redirect()->to('/'); 
        }

        // Load top-viewed properties (uses property view logs)
        $pvModel = new \App\Models\PropertyViewLogsModel();
        $topViewed = $pvModel->getTopViewed(3);

        return view('Pages/client/homepage', [
                'UserID' => session()->get('UserID'),
                'email' => session()->get('inputEmail'),
                'fullname' => trim(session()->get('FirstName') . ' ' . session()->
                get('LastName')),
                'currentUserId' => session()->get('UserID'),
                'otherUser' => null,
                'topViewed' => $topViewed
            ]);
    }

    public function ClientBrowse()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/'); // not logged in
        }

         if ($session->get('role') !== 'Client') {
            return redirect()->to('/'); 
        }


         return view('Pages/client/browse', [
                'UserID' => session()->get('UserID'),
                'email' => session()->get('inputEmail'),
                'fullname' => trim(session()->get('FirstName') . ' ' . session()->
                get('LastName')),
                'currentUserId' => session()->get('UserID'),
                'otherUser' => null
            ]);
    }

    public function ClientBookings()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/'); // not logged in
        }

         if ($session->get('role') !== 'Client') {
            return redirect()->to('/'); 
        }


       return view('Pages/client/bookings', [
                'UserID' => session()->get('UserID'),
                'email' => session()->get('inputEmail'),
                'fullname' => trim(session()->get('FirstName') . ' ' . session()->
                get('LastName')),
                'currentUserId' => session()->get('UserID'),
                'otherUser' => null
            ]);
    }

    public function ClientReservations()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/'); // not logged in
        }

         if ($session->get('role') !== 'Client') {
            return redirect()->to('/'); 
        }


       return view('Pages/client/reservations', [
                'UserID' => session()->get('UserID'),
                'email' => session()->get('inputEmail'),
                'fullname' => trim(session()->get('FirstName') . ' ' . session()->
                get('LastName')),
                'currentUserId' => session()->get('UserID'),
                'otherUser' => null
            ]);
    }

    /**
     * Get reservations for the logged-in client
     * GET /bookings/reservations
     * Also includes scheduled bookings that can be reserved
     */
    public function getReservations()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Client') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $userId = $session->get('UserID');
        $reservationModel = new \App\Models\ReservationModel();
        $reservations = $reservationModel->getReservationsWithDetails($userId);

        // Also get scheduled bookings that can be reserved (not yet in reservations)
        $bookingModel = new BookingModel();
        
        // Get all reservation booking IDs to exclude
        $existingReservationBookingIds = [];
        foreach ($reservations as $res) {
            if (!empty($res['bookingID'])) {
                $existingReservationBookingIds[] = $res['bookingID'];
            }
        }
        $db = \Config\Database::connect();
        $bookingFields = $db->getFieldNames('booking');
        $scheduledBookingsQuery = $db->table('booking');
        $selectSql = '\n                booking.bookingID,\n                booking.BookingDate AS bookingDate,\n                booking.Status AS BookingStatus,\n                booking.Reason,\n                booking.Notes,\n                property.PropertyID,\n                property.Title AS PropertyTitle,\n                property.Description AS PropertyDescription,\n                property.Property_Type,\n                property.Location AS PropertyLocation,\n                property.Size AS PropertySize,\n                property.Bedrooms AS PropertyBedrooms,\n                property.Bathrooms AS PropertyBathrooms,\n                property.Parking_Spaces AS PropertyParking,\n                property.agent_assigned,\n                property.Corporation,\n                property.Price AS PropertyPrice\n            ';

        if (in_array('Purpose', $bookingFields, true)) {
            $scheduledBookingsQuery->select($selectSql)
                ->join('property', 'property.PropertyID = booking.PropertyID', 'left')
                ->where('booking.UserID', $userId)
                // Only include scheduled bookings intended for reservation (Purpose = 'Reserve')
                ->where('booking.Status', 'Scheduled')
                ->where("(booking.Purpose = 'Reserve' OR LOWER(booking.Reason) LIKE '%reserve%')", null, false);
        } else {
            $scheduledBookingsQuery->select($selectSql)
                ->join('property', 'property.PropertyID = booking.PropertyID', 'left')
                ->where('booking.UserID', $userId)
                // Only include scheduled bookings intended for reservation (Reason contains 'reserve')
                ->where('booking.Status', 'Scheduled')
                ->where("LOWER(booking.Reason) LIKE '%reserve%'", null, false);
        }
        if (!empty($existingReservationBookingIds)) {
            $scheduledBookingsQuery->whereNotIn('booking.bookingID', $existingReservationBookingIds);
        }
        
        $scheduledBookings = $scheduledBookingsQuery->get()->getResultArray();
        
        // Normalize Status for scheduled bookings
        foreach ($scheduledBookings as &$sb) {
            if (empty($sb['BookingStatus']) || trim($sb['BookingStatus']) === '') {
                $sb['BookingStatus'] = 'Scheduled';
            }
        }
        unset($sb);

        // Combine reservations and scheduled bookings
        $allReservations = array_merge($reservations, $scheduledBookings);

        // Attach images
        $imgModel = new PropertyImageModel();
        foreach ($allReservations as &$r) {
            $propId = $r['PropertyID'] ?? null;
            $images = [];
            if ($propId) {
                $imgs = $imgModel->where('PropertyID', $propId)->findAll();
                if (!empty($imgs)) {
                    foreach ($imgs as $i) {
                        $images[] = base_url('uploads/properties/' . ($i['Image'] ?? 'no-image.jpg'));
                    }
                } else {
                    $images[] = base_url('uploads/properties/no-image.jpg');
                }
            }
            $r['Images'] = $images;
            $r['reservationID'] = $r['reservationID'] ?? $r['ReservationID'] ?? null;
        }
        unset($r);

        return $this->response->setJSON($allReservations);
    }

    public function ClientProfile()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/'); // not logged in
        }

         if ($session->get('role') !== 'Client') {
            return redirect()->to('/'); 
        }

        // Load full user record so the view can show profile image and other fields
        $usersModel = new UsersModel();
        $user = $usersModel->find(session()->get('UserID'));

        return view('Pages/client/profile', [
                'UserID' => session()->get('UserID'),
                'email' => session()->get('inputEmail'),
                'fullname' => trim(session()->get('FirstName') . ' ' . session()->get('LastName')),
                'currentUserId' => session()->get('UserID'),
                'otherUser' => null,
                'user' => $user
            ]);
    }

      public function cleintChat()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/'); // not logged in
        }

         if ($session->get('role') !== 'Client') {
            return redirect()->to('/'); 
        }

        
        $session = session();
        $clientID = $session->get('UserID');

        $chatSessionModel = new \App\Models\ChatSessionModel();
        $messageModel = new \App\Models\MessageModel();
        $userModel = new \App\Models\UsersModel();

        // Get all chat sessions for this agent
        $sessions = $chatSessionModel->getSessionsByUserId($clientID);

        // Prepare user info for sidebar
        $clients = [];
        foreach ($sessions as $s) {
            $user = $userModel->find($s['AgentID']);
            if ($user) {
                $clients[] = [
                    'chatSessionID' => $s['chatSessionID'],
                    'fullname' => trim($user['FirstName'] . ' ' . $user['LastName']),
                    'lastMessage' => $messageModel
                        ->where('chatSessionID', $s['chatSessionID'])
                        ->orderBy('timestamp', 'DESC')
                        ->first()['messageText'] ?? 'No messages yet'
                ];
            }
        }

        return view('Pages/client/chat', [
            'UserID' => $clientID,
            'fullname' => trim($session->get('FirstName') . ' ' . $session->get('LastName')),
            'clients' => $clients,
        ]);
    }


   

    public function logoutClient(): ResponseInterface
    {

        $userModel = new \App\Models\UsersModel();
        $userID = session()->get('UserID');

        $userModel->setOnlineToOffline($userID);
        session()->destroy();
        return redirect()->to('/');
    }

        // app/Controllers/BookingController.php  (or update UserController::create)
    public function create()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Client') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        // Robust input parsing: prefer JSON if Content-Type indicates JSON,
        // otherwise use getPost() (form-encoded or multipart/form-data).
        $contentType = $this->request->getHeaderLine('Content-Type');
        $input = [];

        if (strpos($contentType, 'application/json') !== false) {
            // safe JSON parse (getJSON throws on invalid JSON -> catch it)
            try {
                $input = $this->request->getJSON(true) ?? [];
            } catch (\Throwable $e) {
                log_message('warning', 'Invalid JSON in bookings/create: ' . $e->getMessage());
                // fallback to raw body attempt
                $raw = $this->request->getBody();
                $json = json_decode($raw, true);
                $input = (json_last_error() === JSON_ERROR_NONE) ? $json : [];
            }
        } else {
            // form-encoded or multipart
            $post = $this->request->getPost(); // returns array of POST values
            if (!empty($post)) {
                $input = $post;
            } else {
                // last fallback: parse raw body (e.g., some clients send urlencoded in raw body)
                $raw = $this->request->getBody();
                parse_str($raw, $parsed);
                $input = !empty($parsed) ? $parsed : [];
            }
        }

        // Now parse/normalize input fields
        $propertyID = $input['property_id'] ?? $input['propertyID'] ?? null;
        $bookingDate = $input['booking_date'] ?? $input['bookingDate'] ?? null; // optional for clients
        $purpose = $input['booking_purpose'] ?? $input['purpose'] ?? null;
        $notes = $input['booking_notes'] ?? $input['notes'] ?? null;

        // property_id is required; bookingDate is optional (agents will assign dates)
        if (empty($propertyID)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'property_id is required']);
        }

        // Normalize purpose value for DB (Purpose column) and determine default status
        $purposeNorm = strtolower(trim((string)$purpose ?? ''));
        if (in_array($purposeNorm, ['reservation','reserve'])) {
            $purpose = 'Reserve';
        } else {
            $purpose = 'Viewing';
        }

        // If a booking date/time is provided at creation, treat it as Scheduled
        if (!empty($bookingDate)) {
            $status = 'Scheduled';
        } else {
            $status = 'Pending';
        }

        // Save booking using your BookingModel
        $bookingModel = new \App\Models\BookingModel();
        $now = date('Y-m-d H:i:s');
        // Use direct DB insert since columns are PascalCase in database
        $db = \Config\Database::connect();
        // Only include Purpose column when it exists in the database schema
        $bookingFields = $db->getFieldNames('booking');
        $data = [
            'UserID'     => $session->get('UserID'),
            'PropertyID' => $propertyID,
            // Allow NULL bookingDate for client-created bookings; agents will set the date later
            'BookingDate'=> !empty($bookingDate) ? $bookingDate : null,
            'Status'     => $status,
            'Reason'     => $purpose,
            'Notes'      => $notes,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        if (in_array('Purpose', $bookingFields, true)) {
            $data['Purpose'] = $purpose;
        }

        try {
            $db->table('booking')->insert($data);
            $insertId = $db->insertID();
            if (!$insertId) {
                log_message('error', 'Booking insert failed');
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to create booking']);
            }

            return $this->response->setJSON(['success' => true, 'bookingID' => $insertId, 'status' => $status, 'purpose' => $purpose]);
        } catch (\Throwable $e) {
            log_message('error', 'Booking create exception: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Server error while creating booking']);
        }
    }

     public function mine()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Client') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $userId = $session->get('UserID');

        $bookingModel = new BookingModel();
        // select booking fields and joined property data
        // Note: Database uses Status (capital S) and some bookings may have empty Status
        $db = \Config\Database::connect();
        $bookingFields = $db->getFieldNames('booking');

        $selectFields = [
            'booking.bookingID',
            'booking.BookingDate AS bookingDate',
        ];

        if (in_array('Purpose', $bookingFields, true)) {
            $selectFields[] = 'booking.Purpose';
        }

        $selectFields = array_merge($selectFields, [
            'booking.Status AS BookingStatus',
            'booking.Reason',
            'booking.Notes',
            'property.PropertyID',
            'property.Title AS PropertyTitle',
            'property.Description AS PropertyDescription',
            'property.Property_Type',
            'property.Location AS PropertyLocation',
            'property.Size AS PropertySize',
            'property.Bedrooms AS PropertyBedrooms',
            'property.Bathrooms AS PropertyBathrooms',
            'property.Parking_Spaces AS PropertyParking',
            'property.agent_assigned',
            'property.Corporation',
            'property.Price AS PropertyPrice'
        ]);

        $selectStr = implode(",\n                ", $selectFields);

        // Build where condition depending on whether Purpose column exists
        if (in_array('Purpose', $bookingFields, true)) {
            $whereCond = "(booking.Purpose = 'Viewing' OR LOWER(booking.Reason) LIKE '%view%')";
        } else {
            $whereCond = "LOWER(booking.Reason) LIKE '%view%'";
        }

        $bookings = $bookingModel
            ->select("\n                {$selectStr}\n            ")
            ->join('property', 'property.PropertyID = booking.PropertyID', 'left')
            ->where('booking.UserID', $userId)
            // Only return bookings intended for Viewing (Purpose='Viewing' or Reason contains 'view')
            ->where($whereCond, null, false)
            ->orderBy('booking.BookingDate', 'DESC')
            ->findAll();
        
        // Debug: log what we're getting from the database
        log_message('debug', "Fetched " . count($bookings) . " bookings for user {$userId}");
        foreach ($bookings as $idx => $b) {
            $status = $b['BookingStatus'] ?? 'NULL';
            log_message('debug', "Booking {$idx}: ID={$b['bookingID']}, Status='{$status}'");
        }
        
        // Normalize empty Status to 'Pending' for display
        foreach ($bookings as &$b) {
            if (empty($b['BookingStatus']) || trim($b['BookingStatus']) === '') {
                $b['BookingStatus'] = 'Pending';
            }
        }
        unset($b);

        // attach images array for each property (optional / lightweight)
        $imgModel = new PropertyImageModel();
        foreach ($bookings as &$b) {
            $propId = $b['PropertyID'] ?? null;
            $images = [];
            if ($propId) {
                $imgs = $imgModel->where('PropertyID', $propId)->findAll();
                if (!empty($imgs)) {
                    foreach ($imgs as $i) {
                        $images[] = base_url('uploads/properties/' . ($i['Image'] ?? 'no-image.jpg'));
                    }
                } else {
                    // fallback: no images in gallery
                    $images[] = base_url('uploads/properties/no-image.jpg');
                }
            }
            $b['Images'] = $images;
        }
        unset($b);

        // Attach DB-backed rating for each booking if a reviews table exists.
        // This is done in a safe try/catch so apps without a reviews table keep working.
        $db = \Config\Database::connect();
        foreach ($bookings as &$b) {
            $b['Rating'] = null;
            try {
                // Attempt to read the latest review for this booking (if table exists)
                $review = $db->table('reviews')
                    ->select('rating')
                    ->where('bookingID', $b['bookingID'])
                    ->orderBy('created_at', 'DESC')
                    ->limit(1)
                    ->get()
                    ->getRowArray();

                if ($review && array_key_exists('rating', $review)) {
                    $b['Rating'] = $review['rating'];
                }
            } catch (\Throwable $e) {
                // Table doesn't exist or other DB error — leave Rating as null
                $b['Rating'] = null;
            }
        }
        unset($b);

        return $this->response->setJSON($bookings);
    }

    public function cancel()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Client') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        // Parse input safely for form-encoded or JSON
        $contentType = $this->request->getHeaderLine('Content-Type');
        $input = [];
        if (strpos($contentType, 'application/json') !== false) {
            try {
                $input = $this->request->getJSON(true) ?? [];
            } catch (\Throwable $e) {
                $input = [];
            }
        } else {
            $input = $this->request->getPost() ?? [];
            if (empty($input)) {
                // fallback parse raw body
                parse_str($this->request->getBody(), $input);
            }
        }

        $bookingId = $input['booking_id'] ?? $input['bookingID'] ?? null;
        $reservationId = $input['reservation_id'] ?? $input['reservationID'] ?? null;
        $status = $input['status'] ?? 'Cancelled';

        if (empty($bookingId)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'booking_id is required']);
        }

        $bookingModel = new \App\Models\BookingModel();
        $booking = $bookingModel->find($bookingId);
        if (!$booking) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Booking not found']);
        }

        // Ownership check
        $userId = $session->get('UserID');
        $bookingUserId = $booking['UserID'] ?? $booking['userID'] ?? null;
        if ($bookingUserId != $userId) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'You do not own this booking']);
        }

        try {
            $now = date('Y-m-d H:i:s');
            
            // If this is a reservation cancellation, delete the reservation record
            if (!empty($reservationId)) {
                $reservationModel = new \App\Models\ReservationModel();
                $reservation = $reservationModel->find($reservationId);
                if ($reservation && $reservation['bookingID'] == $bookingId) {
                    $reservationModel->delete($reservationId);
                }
            }
            
            // Use direct DB update to ensure PascalCase Status column works
            $db = \Config\Database::connect();
            $updated = $db->table('booking')->where('bookingID', $bookingId)->update([
                'Status' => $status,
                'updated_at' => $now
            ]);

            if ($updated === false) {
                $errors = method_exists($bookingModel, 'errors') ? $bookingModel->errors() : null;
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to update booking', 'details' => $errors]);
            }

            return $this->response->setJSON(['success' => true, 'bookingID' => $bookingId, 'status' => $status]);
        } catch (\Exception $e) {
            log_message('error', 'Booking cancel failed: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Server error']);
        }
    }

    /**
     * Client proposes a contract for a booking (persist proposal).
     * POST: booking_id, mode, monthly
     */
    public function proposeContract()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Client') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $post = $this->request->getPost();
        if (empty($post)) {
            parse_str($this->request->getBody(), $post);
        }

        $bookingID = $post['booking_id'] ?? $post['bookingID'] ?? null;
        $mode = $post['mode'] ?? null;
        $monthly = $post['monthly'] ?? null;

        if (empty($bookingID) || empty($mode)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'booking_id and mode are required']);
        }

        $contractModel = new \App\Models\ContractModel();
        $now = date('Y-m-d H:i:s');
        $data = [
            'bookingID' => $bookingID,
            'mode' => $mode,
            'monthly' => $monthly ?? null,
            'proposedBy' => $session->get('UserID'),
            'status' => 'proposed',
            'created_at' => $now,
            'updated_at' => $now,
        ];

        try {
            $id = $contractModel->insert($data);
            if ($id === false) {
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to save contract proposal']);
            }

            return $this->response->setJSON(['success' => true, 'contractID' => $contractModel->getInsertID()]);
        } catch (\Throwable $e) {
            log_message('error', 'proposeContract failed: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Server error']);
        }
    }

    /**
     * Agent confirms a contract proposal.
     * POST: contract_id
     */
    public function confirmContract()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Agent') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $post = $this->request->getPost();
        if (empty($post)) {
            parse_str($this->request->getBody(), $post);
        }

        $contractID = $post['contract_id'] ?? $post['contractID'] ?? null;
        if (empty($contractID)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'contract_id is required']);
        }

        $contractModel = new \App\Models\ContractModel();
        $contract = $contractModel->find($contractID);
        if (!$contract) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Contract not found']);
        }

        try {
            $now = date('Y-m-d H:i:s');
            $updated = $contractModel->update($contractID, [
                'status' => 'confirmed',
                'confirmedBy' => $session->get('UserID'),
                'confirmed_at' => $now,
                'updated_at' => $now,
            ]);

            if ($updated === false) {
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to confirm contract']);
            }

            return $this->response->setJSON(['success' => true]);
        } catch (\Throwable $e) {
            log_message('error', 'confirmContract failed: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Server error']);
        }
    }

    /**
     * Change password for logged-in user
     * Expects JSON { current_password, new_password, confirm_password }
     */
    public function changePassword()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        // Accept JSON or form-urlencoded
        $contentType = $this->request->getHeaderLine('Content-Type');
        if (strpos($contentType, 'application/json') !== false) {
            try {
                $input = $this->request->getJSON(true) ?? [];
            } catch (\Throwable $e) {
                return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid JSON body']);
            }
        } else {
            $input = $this->request->getPost() ?? [];
            if (empty($input)) {
                parse_str($this->request->getBody(), $input);
            }
        }

        $current = $input['current_password'] ?? null;
        $new = $input['new_password'] ?? null;
        $confirm = $input['confirm_password'] ?? null;

        if (empty($current) || empty($new) || empty($confirm)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'All password fields are required']);
        }

        if ($new !== $confirm) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'New password and confirmation do not match']);
        }

        // Basic strength: at least 8 chars, contain letter and number
        if (strlen($new) < 8 || !preg_match('/[A-Za-z]/', $new) || !preg_match('/[0-9]/', $new)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Password must be at least 8 characters and include letters and numbers']);
        }

        $usersModel = new UsersModel();
        $userId = $session->get('UserID');
        $user = $usersModel->find($userId);
        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'User not found']);
        }

        if (!isset($user['Password']) || !password_verify($current, $user['Password'])) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Current password is incorrect']);
        }

        try {
            $hash = password_hash($new, PASSWORD_BCRYPT);
            $updated = $usersModel->update($userId, ['Password' => $hash, 'updated_at' => date('Y-m-d H:i:s')]);
            if ($updated === false) {
                return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Failed to update password']);
            }

            return $this->response->setJSON(['status' => 'success', 'message' => 'Password changed successfully']);
        } catch (\Throwable $e) {
            log_message('error', 'Password change failed: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Server error']);
        }
    }

    /**
     * Client reserves a scheduled booking - moves from bookings to reservations
     * POST: booking_id
     */
    public function reserve()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Client') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $post = $this->request->getPost();
        if (empty($post)) {
            parse_str($this->request->getBody(), $post);
        }

        $bookingId = $post['booking_id'] ?? $post['bookingID'] ?? null;
        if (empty($bookingId)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'booking_id is required']);
        }

        $bookingModel = new \App\Models\BookingModel();
        $booking = $bookingModel->find($bookingId);
        if (!$booking) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Booking not found']);
        }

        // Check ownership and status
        $userId = $session->get('UserID');
        if ($booking['userID'] != $userId) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'You do not own this booking']);
        }
        if ($booking['status'] !== 'Scheduled') {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Only scheduled bookings can be reserved']);
        }

        // Create reservation record
        $reservationModel = new \App\Models\ReservationModel();
        $reservationData = [
            'bookingID' => $bookingId,
            'DownPayment' => 0, // Will be set during payment selection
            'Term_Months' => null, // Will be calculated
            'Monthly_Amortization' => null, // Will be calculated
            'Status' => 'Ongoing'
        ];

        try {
            // Ensure DB connection is available for direct table operations
            $db = \Config\Database::connect();

            $db->table('houserreservation')->insert($reservationData);
            $reservationId = $db->insertID();
            if (!$reservationId) {
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to create reservation']);
            }

            // Update booking status to indicate it's now a reservation
            // Use direct DB update since Status column is PascalCase
            $db->table('booking')->where('bookingID', $bookingId)->update([
                'Status' => 'Reserved',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON(['success' => true, 'reservationID' => $reservationId]);
        } catch (\Throwable $e) {
            log_message('error', 'Reserve booking failed: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Client selects payment method and calculates terms
     * POST: reservation_id, mode (pagibig|banko|full), property_price
     */
    public function selectPayment()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Client') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $post = $this->request->getPost();
        if (empty($post)) {
            parse_str($this->request->getBody(), $post);
        }

        $reservationId = $post['reservation_id'] ?? $post['reservationID'] ?? null;
        $mode = $post['mode'] ?? null;
        $propertyPrice = $post['property_price'] ?? null;

        if (empty($reservationId) || empty($mode) || empty($propertyPrice)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'reservation_id, mode, and property_price are required']);
        }

        // Get user's birthdate for age calculation
        $usersModel = new \App\Models\UsersModel();
        $user = $usersModel->find($session->get('UserID'));
        if (!$user || empty($user['Birthdate'])) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'User birthdate not found']);
        }

        $birthdate = new \DateTime($user['Birthdate']);
        $today = new \DateTime();
        $age = $today->diff($birthdate)->y;

        // Calculate loan terms based on mode
        $maxYears = 0;
        switch (strtolower($mode)) {
            case 'pagibig':
                $maxYears = 60;
                break;
            case 'banko':
                $maxYears = 30;
                break;
            case 'full':
                // No calculation needed for full payment
                break;
            default:
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid payment mode']);
        }

        $calculation = [];
        if ($mode !== 'full') {
            // Formula: years = maxYears (use full loan term available)
            // months = years * 12
            // monthly_payment = property_price / months
            $years = $maxYears; // Use the full maximum term for the loan mode
            $months = $years * 12;
            $monthlyPayment = $propertyPrice / $months;

            $calculation = [
                'age' => $age,
                'max_years' => $maxYears,
                'years' => $years,
                'months' => $months,
                'monthly_payment' => round($monthlyPayment, 2)
            ];
        } else {
            // Full payment: no monthly calculation, just the property price
            $calculation = [
                'age' => $age,
                'monthly_payment' => $propertyPrice,
                'years' => 0,
                'months' => 0
            ];
        }

        // Update reservation with payment details
        $db = \Config\Database::connect();
        $reservation = $db->table('houserreservation')->where('reservationID', $reservationId)->get()->getRowArray();
        if (!$reservation) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Reservation not found']);
        }

        // Check ownership through booking
        $bookingModel = new \App\Models\BookingModel();
        $booking = $bookingModel->find($reservation['bookingID']);
        $bookingUserId = $booking['UserID'] ?? $booking['userID'] ?? null;
        if ($bookingUserId != $session->get('UserID')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'You do not own this reservation']);
        }

        try {
            $updateData = [
                'Term_Months' => $calculation['months'] ?? null,
                'Monthly_Amortization' => $calculation['monthly_payment'] ?? $propertyPrice,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $db->table('houserreservation')->where('reservationID', $reservationId)->update($updateData);

            return $this->response->setJSON([
                'success' => true,
                'calculation' => $calculation,
                'mode' => $mode
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Select payment failed: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Server error']);
        }
    }

    /**
     * Client signs contract with digital signature
     * POST: reservation_id, signature (base64 image)
     */
    public function signContract()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Client') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $post = $this->request->getPost();
        if (empty($post)) {
            parse_str($this->request->getBody(), $post);
        }

        $reservationId = $post['reservation_id'] ?? $post['reservationID'] ?? null;
        $bookingId = $post['booking_id'] ?? $post['bookingID'] ?? null;
        $signature = $post['signature'] ?? null;

        if (empty($reservationId) && empty($bookingId)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'reservation_id or booking_id is required']);
        }
        if (empty($signature)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'signature is required']);
        }

        $db = \Config\Database::connect();
        $reservation = null;
        if (!empty($reservationId)) {
            $reservation = $db->table('houserreservation')->where('reservationID', $reservationId)->get()->getRowArray();
        } elseif (!empty($bookingId)) {
            $reservation = $db->table('houserreservation')->where('bookingID', $bookingId)->get()->getRowArray();
            if (!$reservation) {
                // create a reservation record for this booking
                $insertData = [
                    'bookingID' => $bookingId,
                    'DownPayment' => 0,
                    'Term_Months' => null,
                    'Monthly_Amortization' => null,
                    'Status' => 'PendingConfirmation',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $db->table('houserreservation')->insert($insertData);
                $newId = $db->insertID();
                if ($newId) {
                    $reservation = $db->table('houserreservation')->where('reservationID', $newId)->get()->getRowArray();
                    $reservationId = $newId;
                }
            } else {
                $reservationId = $reservation['reservationID'] ?? $reservation['ReservationID'] ?? $reservationId;
            }
        }

        if (!$reservation) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Reservation not found or could not be created']);
        }

        // Check ownership through booking
        $bookingModel = new \App\Models\BookingModel();
        $booking = $bookingModel->find($reservation['bookingID']);
        $bookingUserId = $booking['UserID'] ?? $booking['userID'] ?? null;
        if ($bookingUserId != $session->get('UserID')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'You do not own this reservation']);
        }

        try {
            // Decode base64 signature and save as blob
            $signatureData = base64_decode(preg_replace('#^data:image/[^;]+;base64,#', '', $signature));
            if ($signatureData === false) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid signature format']);
            }

            // Update reservation with signature
            $db->table('houserreservation')->where('reservationID', $reservationId)->update([
                'Buyer_Signature' => $signatureData,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Generate PDF contract (saves file and returns url)
            $pdfPath = $this->generateContractPDF($reservation, $booking, $signature);

            // Mark reservation as pending admin confirmation (do not mark Completed)
            $db->table('houserreservation')->where('reservationID', $reservationId)->update([
                'Status' => 'PendingConfirmation',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Contract signed and PDF generated. Awaiting admin confirmation.',
                'pdf_url' => $pdfPath,
                'reservationID' => $reservationId
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Sign contract failed: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate PDF contract from reservation data
     */
    private function generateContractPDF($reservation, $booking, $signatureBase64)
    {
        // Get user and property data from database
        $usersModel = new UsersModel();
        $bookingUserId = $booking['UserID'] ?? $booking['userID'] ?? null;
        $client = $usersModel->find($bookingUserId);
        
        if (!$client) {
            throw new \Exception('Client not found in database');
        }
        
        $propertyModel = new \App\Models\PropertyModel();
        $bookingPropertyId = $booking['PropertyID'] ?? $booking['propertyID'] ?? null;
        $property = $propertyModel->find($bookingPropertyId);
        
        if (!$property) {
            throw new \Exception('Property not found in database');
        }
        
        // Get agent assigned to property
        $agent = null;
        if (!empty($property['agent_assigned'])) {
            $agent = $usersModel->where('UserID', $property['agent_assigned'])->where('Role', 'Agent')->first();
        }
        
        // Extract property address components (if Location contains full address)
        $propertyLocation = $property['Location'] ?? '';
        $addressParts = explode(',', $propertyLocation);
        $streetAddress = trim($addressParts[0] ?? $propertyLocation);
        $city = trim($addressParts[1] ?? '');
        $stateProvince = trim($addressParts[2] ?? '');
        $postalZip = trim($addressParts[3] ?? '');
        
        // Calculate contract dates
        $startDate = date('Y-m-d', strtotime($booking['BookingDate'] ?? $booking['bookingDate'] ?? 'now'));
        $termMonths = $reservation['Term_Months'] ?? 0;
        $terminationDate = date('Y-m-d', strtotime('+' . $termMonths . ' months', strtotime($startDate)));
        
        // Prepare comprehensive contract data from database
        $contractData = [
            // Agent/Team Leader information
            'teamLeaderFirstName' => $agent ? ($agent['FirstName'] ?? '') : 'Not Assigned',
            'teamLeaderLastName' => $agent ? ($agent['LastName'] ?? '') : '',
            'teamLeaderEmail' => $agent ? ($agent['Email'] ?? '') : '',
            'teamLeaderPhone' => $agent ? ($agent['phoneNumber'] ?? $agent['phone'] ?? '') : '',
            
            // Client information from database
            'clientFirstName' => $client['FirstName'] ?? '',
            'clientMiddleName' => $client['MiddleName'] ?? '',
            'clientLastName' => $client['LastName'] ?? '',
            'clientEmail' => $client['Email'] ?? '',
            'clientPhone' => $client['phoneNumber'] ?? $client['phone'] ?? '',
            'clientBirthdate' => $client['Birthdate'] ?? '',
            
            // Property information from database
            'propertyTitle' => $property['Title'] ?? '',
            'streetAddress' => $streetAddress,
            'city' => $city,
            'stateProvince' => $stateProvince,
            'postalZip' => $postalZip,
            'propertyType' => $property['Property_Type'] ?? '',
            'propertySize' => $property['Size'] ?? '',
            'propertyBedrooms' => $property['Bedrooms'] ?? '',
            'propertyBathrooms' => $property['Bathrooms'] ?? '',
            'propertyPrice' => number_format($property['Price'] ?? 0, 2),
            
            // Contract terms from reservation
            'termOfContract' => $termMonths > 0 ? $termMonths . ' months' : 'Not specified',
            'startDate' => $startDate,
            'termination' => $terminationDate,
            'monthlyPayment' => number_format($reservation['Monthly_Amortization'] ?? 0, 2),
            'deposit' => number_format($reservation['DownPayment'] ?? 0, 2),
            'furnishings' => $property['Description'] ?? 'As per property listing',
            
            // Signature and date
            'signDay' => date('d'),
            'signMonth' => date('F'),
            'signYear' => date('Y'),
            'signature' => $signatureBase64
        ];
        
        // Load dompdf
        $dompdf = new \Dompdf\Dompdf();
        
        // Generate HTML for contract
        $html = $this->generateContractHTML($contractData);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Save PDF
        $pdfDir = WRITEPATH . 'contracts/';
        if (!is_dir($pdfDir)) {
            mkdir($pdfDir, 0755, true);
        }
        
        $reservationId = $reservation['reservationID'] ?? $reservation['ReservationID'] ?? time();
        $filename = 'contract_' . $reservationId . '_' . time() . '.pdf';
        $filepath = $pdfDir . $filename;
        file_put_contents($filepath, $dompdf->output());
        
        return base_url('writable/contracts/' . $filename);
    }

    /**
     * Generate HTML for contract agreement
     */
    private function generateContractHTML($data)
    {


        $signatureImg = '<img src="' . $data['signature'] . '" style="max-width: 200px; max-height: 80px;" />';
        
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; padding: 40px; line-height: 1.6; }
                .header { border-bottom: 4px solid #2563eb; padding: 20px 0; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
                .header h1 { color: #1e40af; margin: 0; }
                section { margin-bottom: 30px; }
                h2 { color: #1f2937; margin-bottom: 15px; }
                .form-field { display: inline-block; border-bottom: 1px solid #000; min-width: 150px; padding: 0 5px; }
                .signature-section { margin-top: 50px; padding-top: 30px; border-top: 2px solid #e5e7eb; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>CONTRACT AGREEMENT</h1>
            </div>
            
            <section>
                <h2>1. Parties, Leased property, Term and Payment</h2>
                <p>This Contract Agreement is made by and between:</p>
                
                <p><strong>Team Leader:</strong> ' . htmlspecialchars(trim($data['teamLeaderFirstName'] . ' ' . $data['teamLeaderLastName'])) . 
                ($data['teamLeaderEmail'] ? ' (' . htmlspecialchars($data['teamLeaderEmail']) . ')' : '') . '</p>
                
                <p><strong>Client:</strong> ' . htmlspecialchars(trim($data['clientFirstName'] . ' ' . ($data['clientMiddleName'] ? $data['clientMiddleName'] . ' ' : '') . $data['clientLastName'])) . 
                ($data['clientEmail'] ? ' (' . htmlspecialchars($data['clientEmail']) . ')' : '') . 
                ($data['clientPhone'] ? ' | Phone: ' . htmlspecialchars($data['clientPhone']) : '') . '</p>
                
                <p>The Team Leader hereby agrees to lease the Property located at:</p>
                <p><strong>' . htmlspecialchars($data['propertyTitle']) . '</strong></p>
                <p>' . htmlspecialchars($data['streetAddress']) . 
                ($data['city'] ? ', ' . htmlspecialchars($data['city']) : '') . 
                ($data['stateProvince'] ? ', ' . htmlspecialchars($data['stateProvince']) : '') . 
                ($data['postalZip'] ? ' ' . htmlspecialchars($data['postalZip']) : '') . '</p>
                
                <p>Property Details: ' . 
                ($data['propertyType'] ? htmlspecialchars($data['propertyType']) . ' | ' : '') . 
                ($data['propertySize'] ? 'Size: ' . htmlspecialchars($data['propertySize']) . ' | ' : '') . 
                ($data['propertyBedrooms'] ? 'Bedrooms: ' . htmlspecialchars($data['propertyBedrooms']) . ' | ' : '') . 
                ($data['propertyBathrooms'] ? 'Bathrooms: ' . htmlspecialchars($data['propertyBathrooms']) : '') . 
                '</p>
                
                <p>The contract period shall be <span class="form-field">' . htmlspecialchars($data['termOfContract']) . '</span>, 
                starting from <span class="form-field">' . htmlspecialchars($data['startDate']) . '</span> 
                and shall end and may be renewable <span class="form-field">' . htmlspecialchars($data['termination']) . '</span> 
                thereafter, on the agreed amount of ₱<span class="form-field">' . htmlspecialchars($data['monthlyPayment']) . '</span> 
                to be paid monthly, and the amount of ₱<span class="form-field">' . htmlspecialchars($data['deposit']) . '</span> 
                deposit to be paid upon the execution of this contract.</p>
                
                <p><strong>Total Property Price:</strong> ₱' . htmlspecialchars($data['propertyPrice']) . '</p>
            </section>
            
            <section>
                <h2>2. Use of Property</h2>
                <p>The Client shall use the Property only for residential purposes. During the term of this Agreement, the tenant shall act with care and prudence to prevent damage to the Property at all times.</p>
            </section>
            
            <section>
                <h2>3. Utilities</h2>
                <p>The Client agrees to pay for the utilities and other services used in the Property during the term of this Agreement.</p>
            </section>
            
            <section>
                <h2>4. Furnishings</h2>
                <p>The fixture furnishings of the Property are as follows:</p>
                <p>' . htmlspecialchars($data['furnishings']) . '</p>
            </section>
            
            <section>
                <h2>5. Repairs and Damages</h2>
                <p>Any losses and damages to fixture furnishing shall be defrayed by the Client. If any reasonable repair is necessary on the fixture furnishings, the Client shall notify it to the Team Leader. The Team Leader shall defray repair costs of the fixture furnishing.</p>
                <p>The Client is not permitted to modify or paint or materially change any constant part of the Property.</p>
            </section>
            
            <section>
                <h2>6. Termination</h2>
                <p>This Agreement automatically expires at the end of the specifies period above. However, this Agreement shall be renewed by mutual written consent of the Parties at any time.</p>
                <p>Signed on this <span class="form-field">' . htmlspecialchars($data['signDay']) . '</span> day of 
                <span class="form-field">' . htmlspecialchars($data['signMonth']) . '</span>, 
                <span class="form-field">' . htmlspecialchars($data['signYear']) . '</span>.</p>
            </section>
            
            <div class="signature-section">
                <p><strong>Client Signature:</strong></p>
                        ' . $signatureImg . '
                <p>' . htmlspecialchars($data['clientFirstName'] . ' ' . $data['clientLastName']) . '</p>
            </div>
        </body>
        </html>';
    }

            /**
             * Fill existing PDF template (`public/assets/PDFContract.pdf`) with provided form fields and signature image
             * POST: form fields + signature (base64 data URL)
             */
            public function fillTemplatePdf()
            {
                $session = session();
                if (!$session->get('isLoggedIn')) {
                    return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
                }

                $post = $this->request->getPost();
                if (empty($post)) {
                    parse_str($this->request->getBody(), $post);
                }

                $signatureDataUrl = $post['signature'] ?? null;
                if (empty($signatureDataUrl)) {
                    return $this->response->setStatusCode(400)->setJSON(['error' => 'signature is required']);
                }

                // Decode signature and save PNG
                $sigDir = WRITEPATH . 'contracts/signatures/';
                if (!is_dir($sigDir)) mkdir($sigDir, 0755, true);
                $sigBase64 = preg_replace('#^data:image/[^;]+;base64,#i', '', $signatureDataUrl);
                $sigBytes = base64_decode($sigBase64);
                if ($sigBytes === false) {
                    return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid signature data']);
                }
                $sigFilename = 'signature_' . time() . '_' . ($session->get('UserID') ?? 'anon') . '.png';
                $sigPath = $sigDir . $sigFilename;
                file_put_contents($sigPath, $sigBytes);

                // Prepare expected fields (these names should match the React form names)
                $fields = [
                    'teamLeaderFirstName','teamLeaderLastName',
                    'clientFirstName','clientLastName',
                    'streetAddress','city','stateProvince','postalZip',
                    'termOfContract','startDate','termination',
                    'monthlyPayment','deposit','furnishings',
                    'signDay','signMonth','signYear'
                ];

                $data = [];
                foreach ($fields as $f) {
                    $data[$f] = $post[$f] ?? '';
                }

                // Template path (public assets)
                // Prefer `assets/PDFContract/Contract-Agreement.pdf` but fall back to existing `assets/PDFContruct/Contract-Agreement.pdf`.
                $preferred = FCPATH . 'assets/PDFContract/Contract-Agreement.pdf';
                $fallback = FCPATH . 'assets/PDFContruct/Contract-Agreement.pdf';
                if (file_exists($preferred)) {
                    $template = $preferred;
                } elseif (file_exists($fallback)) {
                    $template = $fallback;
                } else {
                    return $this->response->setStatusCode(500)->setJSON(['error' => 'PDF template not found. Checked: ' . $preferred . ' and ' . $fallback]);
                }

                try {
                    $pdf = new Fpdi();

                    $pageCount = $pdf->setSourceFile($template);
                    $tpl = $pdf->importPage(1);

                    $size = $pdf->getTemplateSize($tpl);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($tpl);

                    // Set font and color
                    $pdf->SetFont('Helvetica', '', 10);
                    $pdf->SetTextColor(0,0,0);

                    // Example positions (in mm) -- adjust to match your template
                    // Use precise text boxes (MultiCell) for accurate placement and wrapping
                    $pdf->SetFont('Helvetica', '', 10);
                    // Helper: write text inside a box with width (w) and line height (lh)
                    $writeBox = function($x, $y, $w, $lh, $text) use ($pdf) {
                        $pdf->SetXY($x, $y);
                        $pdf->MultiCell($w, $lh, trim($text), 0, 'L');
                    };

                    // Team leader name box
                    $writeBox(30, 60, 80, 5, $data['teamLeaderFirstName'] . ' ' . $data['teamLeaderLastName']);

                    // Client name box
                    $writeBox(30, 70, 80, 5, $data['clientFirstName'] . ' ' . $data['clientLastName']);

                    // Address box (allow wrapping)
                    $writeBox(30, 80, 120, 5, $data['streetAddress'] . ', ' . $data['city'] . ' ' . $data['postalZip']);

                    // Term and dates (single-line box)
                    $writeBox(30, 95, 100, 5, $data['termOfContract'] . ' starting ' . $data['startDate']);

                    // Monthly payment (right-aligned in its box)
                    $pdf->SetXY(140, 120);
                    $pdf->Cell(50, 5, '₱' . $data['monthlyPayment'], 0, 0, 'R');

                    // Insert signature image (adjust coordinates/width). Use larger width for clarity.
                    $sigX = 30; $sigY = 200; $sigW = 70;
                    $pdf->Image($sigPath, $sigX, $sigY, $sigW, 0, 'PNG');

                    $outDir = WRITEPATH . 'contracts/';
                    if (!is_dir($outDir)) mkdir($outDir, 0755, true);
                    $outFilename = 'contract_filled_' . time() . '_' . ($session->get('UserID') ?? 'anon') . '.pdf';
                    $outPath = $outDir . $outFilename;
                    $pdf->Output($outPath, 'F');

                    $publicUrl = base_url('writable/contracts/' . $outFilename);
                    return $this->response->setJSON(['success' => true, 'pdf_url' => $publicUrl]);
                } catch (\Throwable $e) {
                    log_message('error', 'fillTemplatePdf error: ' . $e->getMessage());
                    return $this->response->setStatusCode(500)->setJSON(['error' => 'PDF generation failed: ' . $e->getMessage()]);
                }
            }

            /**
             * Debug helper: identical to fillTemplatePdf but skips session auth for testing.
             * POST to /debug/fillPdfNoAuth with same fields.
             */
            public function fillTemplatePdfNoAuth()
            {
                $post = $this->request->getPost();
                if (empty($post)) {
                    parse_str($this->request->getBody(), $post);
                }

                $signatureDataUrl = $post['signature'] ?? null;
                if (empty($signatureDataUrl)) {
                    return $this->response->setStatusCode(400)->setJSON(['error' => 'signature is required']);
                }

                // Decode signature and save PNG
                $sigDir = WRITEPATH . 'contracts/signatures/';
                if (!is_dir($sigDir)) mkdir($sigDir, 0755, true);
                $sigBase64 = preg_replace('#^data:image/[^;]+;base64,#i', '', $signatureDataUrl);
                $sigBytes = base64_decode($sigBase64);
                if ($sigBytes === false) {
                    return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid signature data']);
                }
                $sigFilename = 'signature_test_' . time() . '.png';
                $sigPath = $sigDir . $sigFilename;
                file_put_contents($sigPath, $sigBytes);

                // Minimal fields
                $data = [];
                $data['teamLeaderFirstName'] = $post['teamLeaderFirstName'] ?? '';
                $data['teamLeaderLastName'] = $post['teamLeaderLastName'] ?? '';
                $data['clientFirstName'] = $post['clientFirstName'] ?? '';
                $data['clientLastName'] = $post['clientLastName'] ?? '';
                $data['streetAddress'] = $post['streetAddress'] ?? '';
                $data['city'] = $post['city'] ?? '';
                $data['postalZip'] = $post['postalZip'] ?? '';
                $data['termOfContract'] = $post['termOfContract'] ?? '';
                $data['startDate'] = $post['startDate'] ?? '';
                $data['monthlyPayment'] = $post['monthlyPayment'] ?? '';

                // Prefer `assets/PDFContract/Contract-Agreement.pdf` but fall back to `assets/PDFContruct/Contract-Agreement.pdf`.
                $preferred = FCPATH . 'assets/PDFContract/Contract-Agreement.pdf';
                $fallback = FCPATH . 'assets/PDFContruct/Contract-Agreement.pdf';
                if (file_exists($preferred)) {
                    $template = $preferred;
                } elseif (file_exists($fallback)) {
                    $template = $fallback;
                } else {
                    return $this->response->setStatusCode(500)->setJSON(['error' => 'PDF template not found. Checked: ' . $preferred . ' and ' . $fallback]);
                }

                try {
                    $pdf = new Fpdi();
                    $tpl = $pdf->importPage(1, '/MediaBox');
                    $size = $pdf->getTemplateSize($tpl);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($tpl);

                    $pdf->SetFont('Helvetica', '', 10);
                    $pdf->SetTextColor(0,0,0);

                    $pdf->SetFont('Helvetica', '', 10);
                    $writeBox = function($x, $y, $w, $lh, $text) use ($pdf) {
                        $pdf->SetXY($x, $y);
                        $pdf->MultiCell($w, $lh, trim($text), 0, 'L');
                    };

                    $writeBox(30, 60, 80, 5, $data['teamLeaderFirstName'] . ' ' . $data['teamLeaderLastName']);
                    $writeBox(30, 70, 80, 5, $data['clientFirstName'] . ' ' . $data['clientLastName']);
                    $writeBox(30, 80, 120, 5, $data['streetAddress'] . ', ' . $data['city'] . ' ' . $data['postalZip']);
                    $writeBox(30, 95, 100, 5, $data['termOfContract'] . ' starting ' . $data['startDate']);

                    $pdf->SetXY(140, 120);
                    $pdf->Cell(50, 5, '₱' . $data['monthlyPayment'], 0, 0, 'R');

                    $sigX = 30; $sigY = 200; $sigW = 70;
                    $pdf->Image($sigPath, $sigX, $sigY, $sigW, 0, 'PNG');

                    $outDir = WRITEPATH . 'contracts/';
                    if (!is_dir($outDir)) mkdir($outDir, 0755, true);
                    $outFilename = 'contract_filled_test_' . time() . '.pdf';
                    $outPath = $outDir . $outFilename;
                    $pdf->Output($outPath, 'F');

                    $publicUrl = base_url('writable/contracts/' . $outFilename);
                    return $this->response->setJSON(['success' => true, 'pdf_url' => $publicUrl, 'sig_path' => $sigPath]);
                } catch (\Throwable $e) {
                    log_message('error', 'fillTemplatePdfNoAuth error: ' . $e->getMessage());
                    return $this->response->setStatusCode(500)->setJSON(['error' => 'PDF generation failed: ' . $e->getMessage()]);
                }
            }


}
