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

        // Save booking using your BookingModel
        $bookingModel = new \App\Models\BookingModel();
        $now = date('Y-m-d H:i:s');
        $data = [
            'userID'     => $session->get('UserID'),
            'propertyID' => $propertyID,
            // Allow NULL bookingDate for client-created bookings; agents will set the date later
            'bookingDate'=> !empty($bookingDate) ? $bookingDate : null,
            'status'     => 'Pending',
            'Reason'     => $purpose,
            'Notes'      => $notes,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        try {
            $insertId = $bookingModel->insert($data);
            if ($insertId === false) {
                $errors = method_exists($bookingModel, 'errors') ? $bookingModel->errors() : null;
                log_message('error', 'Booking insert failed: ' . json_encode($errors));
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to create booking', 'details' => $errors]);
            }

            return $this->response->setJSON(['success' => true, 'bookingID' => $insertId, 'status' => 'Pending']);
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
        $bookings = $bookingModel
            ->select('
                booking.bookingID,
                booking.bookingDate,
                booking.status AS BookingStatus,
                booking.Reason,
                booking.Notes,
                property.PropertyID,
                property.Title AS PropertyTitle,
                property.Location AS PropertyLocation,
                property.Price AS PropertyPrice
            ')
            ->join('property', 'property.PropertyID = booking.propertyID', 'left')
            ->where('booking.userID', $userId)
            ->orderBy('booking.bookingDate', 'DESC')
            ->findAll();

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
        if (isset($booking['userID']) && $booking['userID'] != $userId) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'You do not own this booking']);
        }

        try {
            $now = date('Y-m-d H:i:s');
            $updated = $bookingModel->update($bookingId, [
                'status' => $status,
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


}
