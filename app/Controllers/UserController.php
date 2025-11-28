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

        // Handle optional file uploads: profile photo and government ID
        // Employment status influences where profile photos are stored
        $employmentStatus = $this->request->getPost('employmentStatus') ?? $this->request->getPost('Employment_Status') ?? null;

        // Prepare upload status for diagnostics
        $uploadStatus = [
            'profilePhoto' => 'not_provided',
            'govIdImage' => 'not_provided'
        ];

        // Profile photo: input name expected 'profilePhoto'
        try {
            $photoFile = $this->request->getFile('profilePhoto');
            if ($photoFile && $photoFile->getName() !== '' ) {
                if ($photoFile->isValid() && !$photoFile->hasMoved()) {
                    // Choose upload directory per user's employment status
                    // Store files under public/uploads/ofw/ or public/uploads/locallyemployed/
                    $subdir = 'locallyemployed';
                    if ($employmentStatus && strcasecmp(trim($employmentStatus), 'ofw') === 0) {
                        $subdir = 'ofw';
                    }
                    $photoDir = FCPATH . 'uploads/' . $subdir . '/';
                    if (!is_dir($photoDir)) {
                        mkdir($photoDir, 0755, true);
                    }
                    $newName = $photoFile->getRandomName();
                    $moved = $photoFile->move($photoDir, $newName);
                    if ($moved) {
                        // Store only the filename in DB; view templates build the full URL using folder
                        $data['Image'] = $newName;
                        $data['employmentStatus'] = $subdir;
                        $uploadStatus['profilePhoto'] = 'saved: ' . $newName . ' in uploads/' . $subdir;
                    } else {
                        $uploadStatus['profilePhoto'] = 'move_failed';
                        log_message('warning', 'Profile photo move failed for uploaded file.');
                    }
                } else {
                    $err = $photoFile->getError();
                    $uploadStatus['profilePhoto'] = 'invalid_or_moved; error=' . $err;
                    log_message('warning', 'Profile photo invalid or already moved. Error code: ' . $err);
                }
            }
        } catch (\Throwable $e) {
            $uploadStatus['profilePhoto'] = 'exception: ' . $e->getMessage();
            log_message('warning', 'Profile photo upload exception: ' . $e->getMessage());
        }

        // Government ID: input name expected 'govIdImage' — move to uploads/governmentid/ for safekeeping.
        // We will not write gov ID fields into `users` table here. Employment-specific tables will receive document filenames after user creation.
        $govName = null;
        try {
            $idFile = $this->request->getFile('govIdImage');
            if ($idFile && $idFile->getName() !== '') {
                if ($idFile->isValid() && !$idFile->hasMoved()) {
                    $govDir = FCPATH . 'uploads/governmentid/';
                    if (!is_dir($govDir)) {
                        mkdir($govDir, 0755, true);
                    }
                    $govName = $idFile->getRandomName();
                    $moved = $idFile->move($govDir, $govName);
                    if ($moved) {
                        $uploadStatus['govIdImage'] = 'saved: ' . $govName . ' in uploads/governmentid';
                    } else {
                        $uploadStatus['govIdImage'] = 'move_failed';
                        log_message('warning', 'Gov ID move failed for uploaded file.');
                    }
                } else {
                    $err = $idFile->getError();
                    $uploadStatus['govIdImage'] = 'invalid_or_moved; error=' . $err;
                    log_message('warning', 'Gov ID invalid or already moved. Error code: ' . $err);
                }
            }
        } catch (\Throwable $e) {
            $uploadStatus['govIdImage'] = 'exception: ' . $e->getMessage();
            log_message('warning', 'Gov ID upload exception: ' . $e->getMessage());
        }

        if ($usersModel->insert($data)) {
            $userID = $usersModel->getInsertID();


            $tokenModel->insert([
                'userID'     => $userID,
                'token'      => $otp_code,
                'expires_at' => $session_expiry,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // After user is created, persist employment-specific documents into the appropriate model/table.
            try {
                $empStatus = $employmentStatus;
                if ($empStatus === 'locally_employed' || strcasecmp($empStatus, 'locally_employed') === 0 || strcasecmp($empStatus, 'locallyemployed') === 0) {
                    $localModel = new \App\Models\LocalEmploymentModel();
                    $localData = [ 'UserID' => $userID ];
                    // Fields: Id_With_Signature, Payslip, proof_of_billing
                    $localFiles = [ 'Id_With_Signature', 'Payslip', 'proof_of_billing' ];
                    foreach ($localFiles as $f) {
                        $file = $this->request->getFile($f);
                        if ($file && $file->getName() !== '' && $file->isValid() && !$file->hasMoved()) {
                            $dir = FCPATH . 'uploads/locallyemployed/';
                            if (!is_dir($dir)) mkdir($dir, 0755, true);
                            $newName = $file->getRandomName();
                            if ($file->move($dir, $newName)) {
                                $localData[$f] = $newName;
                                $uploadStatus[$f] = 'saved: ' . $newName . ' in uploads/locallyemployed';
                            } else {
                                $uploadStatus[$f] = 'move_failed';
                            }
                        }
                    }
                    // If gov ID was uploaded and Id_With_Signature is empty, use gov file as Id_With_Signature
                    if (empty($localData['Id_With_Signature']) && $govName) {
                        // copy/move the gov file into locallyemployed folder to keep employment docs together
                        $source = FCPATH . 'uploads/governmentid/' . $govName;
                        if (is_file($source)) {
                            $destName = $govName; // reuse filename
                            $dest = FCPATH . 'uploads/locallyemployed/' . $destName;
                            if (!is_dir(dirname($dest))) mkdir(dirname($dest), 0755, true);
                            copy($source, $dest);
                            $localData['Id_With_Signature'] = $destName;
                            $uploadStatus['govIdImage_to_localemployment'] = 'copied into uploads/locallyemployed as ' . $destName;
                        }
                    }
                    // Insert employment record if we have any non-empty fields besides UserID
                    $hasDoc = false;
                    foreach ($localData as $k => $v) { if ($k !== 'UserID' && !empty($v)) { $hasDoc = true; break; } }
                    if ($hasDoc) {
                        $localModel->insert($localData);
                    }
                } else if ($empStatus === 'ofw' || strcasecmp($empStatus, 'ofw') === 0 || strcasecmp($empStatus, 'ofw') === 0) {
                    $ofwModel = new \App\Models\OFWModel();
                    $ofwData = [ 'UserID' => $userID ];
                    $ofwFiles = [ 'Job_Contract', 'Passport', 'Official_Identity_Documents' ];
                    foreach ($ofwFiles as $f) {
                        $file = $this->request->getFile($f);
                        if ($file && $file->getName() !== '' && $file->isValid() && !$file->hasMoved()) {
                            $dir = FCPATH . 'uploads/ofw/';
                            if (!is_dir($dir)) mkdir($dir, 0755, true);
                            $newName = $file->getRandomName();
                            if ($file->move($dir, $newName)) {
                                $ofwData[$f] = $newName;
                                $uploadStatus[$f] = 'saved: ' . $newName . ' in uploads/ofw';
                            } else {
                                $uploadStatus[$f] = 'move_failed';
                            }
                        }
                    }
                    // If gov ID was uploaded and Official_Identity_Documents is empty, copy gov file into ofw folder
                    if (empty($ofwData['Official_Identity_Documents']) && $govName) {
                        $source = FCPATH . 'uploads/governmentid/' . $govName;
                        if (is_file($source)) {
                            $destName = $govName;
                            $dest = FCPATH . 'uploads/ofw/' . $destName;
                            if (!is_dir(dirname($dest))) mkdir(dirname($dest), 0755, true);
                            copy($source, $dest);
                            $ofwData['Official_Identity_Documents'] = $destName;
                            $uploadStatus['govIdImage_to_ofw'] = 'copied into uploads/ofw as ' . $destName;
                        }
                    }
                    $hasDoc = false;
                    foreach ($ofwData as $k => $v) { if ($k !== 'UserID' && !empty($v)) { $hasDoc = true; break; } }
                    if ($hasDoc) {
                        $ofwModel->insert($ofwData);
                    }
                }
            } catch (\Throwable $e) {
                log_message('warning', 'Employment docs save exception: ' . $e->getMessage());
                $uploadStatus['employment_docs_exception'] = $e->getMessage();
            }

            $session->remove(['otp_code', 'otp_email', 'otp_expiry']);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'User registered successfully',
                'uploadStatus' => $uploadStatus
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to register user.', 'uploadStatus' => $uploadStatus]);
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

    /**
     * POST /users/updateProfile
     * Update logged-in user's basic profile and avatar
     */
    public function updateProfile()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        $userID = $session->get('UserID');
        if (!$userID) return redirect()->back();

        $usersModel = new UsersModel();
        $user = $usersModel->find($userID);
        if (!$user) return redirect()->back()->with('error', 'User not found');

        $fullName = trim($this->request->getPost('full_name') ?? '');
        $email = trim($this->request->getPost('email') ?? '');
        $phone = trim($this->request->getPost('phone') ?? '');
        $removeAvatar = $this->request->getPost('remove_avatar') === '1';

        $data = [];
        if ($fullName !== '') {
            // naive split into first and last
            $parts = preg_split('/\s+/', $fullName);
            $data['FirstName'] = $parts[0] ?? $fullName;
            $data['LastName'] = isset($parts[1]) ? implode(' ', array_slice($parts,1)) : '';
        }
        if ($email !== '') $data['Email'] = $email;
        if ($phone !== '') $data['phoneNumber'] = $phone;

        // Handle avatar upload
        try {
            $avatarFile = $this->request->getFile('avatar');
            if ($avatarFile && $avatarFile->getName() !== '') {
                if ($avatarFile->isValid() && !$avatarFile->hasMoved()) {
                    $uploadDir = FCPATH . 'uploads/profiles/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                    $newName = $avatarFile->getRandomName();
                    $moved = $avatarFile->move($uploadDir, $newName);
                    if ($moved) {
                        // delete old file if present
                        if (!empty($user['Image'])) {
                            $old = $uploadDir . $user['Image'];
                            if (is_file($old)) @unlink($old);
                        }
                        $data['Image'] = $newName;
                    }
                }
            } elseif ($removeAvatar) {
                // remove existing avatar
                if (!empty($user['Image'])) {
                    $old = FCPATH . 'uploads/profiles/' . $user['Image'];
                    if (is_file($old)) @unlink($old);
                }
                $data['Image'] = null;
            }
        } catch (\Throwable $e) {
            log_message('warning', 'Avatar upload error: ' . $e->getMessage());
        }

        if (!empty($data)) {
            try {
                $usersModel->update($userID, $data);
                // refresh session name/email if present
                if (isset($data['FirstName'])) $session->set('FirstName', $data['FirstName']);
                if (isset($data['LastName'])) $session->set('LastName', $data['LastName']);
                if (isset($data['Email'])) $session->set('inputEmail', $data['Email']);
                $session->setFlashdata('success', 'Profile updated successfully.');
            } catch (\Throwable $e) {
                log_message('error', 'Failed to update profile: ' . $e->getMessage());
                $session->setFlashdata('error', 'Unable to update profile.');
            }
        }

        // Redirect back to role-specific profile page
        $role = $session->get('role') ?? '';
        if (strtolower($role) === 'agent') return redirect()->to('/users/agentprofile');
        return redirect()->to('/users/clientprofile');
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

        // For client-created bookings, treat them as 'Pending' regardless of bookingDate
        // (Agents should set scheduled bookings via agent workflow).
        $status = 'Pending';

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

        // Determine mode (client may request 'reservations' or 'bookings')
        $mode = $this->request->getGet('mode') ?? $this->request->getGet('view') ?? null;

        // Build where condition depending on requested mode and whether Purpose column exists
        if (strtolower((string)$mode) === 'reservations') {
            if (in_array('Purpose', $bookingFields, true)) {
                // Prefer explicit Purpose column when present
                $whereCond = "(booking.Purpose = 'Reserve' OR LOWER(booking.Reason) LIKE '%reserve%')";
            } else {
                $whereCond = "LOWER(booking.Reason) LIKE '%reserve%'";
            }
        } else {
            // Default: bookings page -> Viewing
            if (in_array('Purpose', $bookingFields, true)) {
                $whereCond = "(booking.Purpose = 'Viewing' OR LOWER(booking.Reason) LIKE '%view%')";
            } else {
                $whereCond = "LOWER(booking.Reason) LIKE '%view%'";
            }
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

    /**
     * Return a single booking's details (JSON) for the logged-in client
     * GET /bookings/{id}
     */
    public function getBooking($id = null)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Client') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        if (empty($id) || !is_numeric($id)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid booking id']);
        }

        $userId = $session->get('UserID');
        $bookingModel = new BookingModel();
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

        $booking = $bookingModel
            ->select("\n                {$selectStr}\n            ")
            ->join('property', 'property.PropertyID = booking.PropertyID', 'left')
            ->where('booking.bookingID', $id)
            ->where('booking.UserID', $userId)
            ->first();

        if (!$booking) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Booking not found']);
        }

        // Normalize Status
        if (empty($booking['BookingStatus']) || trim($booking['BookingStatus']) === '') {
            $booking['BookingStatus'] = 'Pending';
        }

        // attach images
        $imgModel = new PropertyImageModel();
        $propId = $booking['PropertyID'] ?? null;
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
        $booking['Images'] = $images;

        // attach rating if reviews table exists
        $booking['Rating'] = null;
        try {
            $review = $db->table('reviews')
                ->select('rating')
                ->where('bookingID', $booking['bookingID'])
                ->orderBy('created_at', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();
            if ($review && array_key_exists('rating', $review)) {
                $booking['Rating'] = $review['rating'];
            }
        } catch (\Throwable $e) {
            // ignore if table not present
        }

        return $this->response->setJSON($booking);
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

            // Create reservation record using the ReservationModel so allowedFields are respected
        $reservationModel = new \App\Models\ReservationModel();
        $reservationData = [
            'bookingID' => $bookingId,
            'DownPayment' => 0, // Will be set during payment selection
            'Term_Months' => null, // Will be calculated
            'Monthly_Amortization' => null, // Will be calculated
            'Status' => 'Ongoing'
        ];

        try {
            // Insert via model to ensure proper fields
            $insertId = $reservationModel->insert($reservationData);
            if ($insertId === false || empty($insertId)) {
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to create reservation']);
            }

            // Update booking status to indicate it's now a reservation
            $db = \Config\Database::connect();
            $db->table('booking')->where('bookingID', $bookingId)->update([
                'Status' => 'Reserved',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON(['success' => true, 'reservationID' => $insertId]);
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
        $reservationModel = new \App\Models\ReservationModel();
        $reservation = $reservationModel->find($reservationId);
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

                // Filter fields to existing columns
                $db->table('reservations')->where('reservationID', $reservationId)->update($this->filterTableFields('reservations', $updateData));

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
        $reservationModel = new \App\Models\ReservationModel();
        if (!empty($reservationId)) {
            $reservation = $reservationModel->find($reservationId);
        } elseif (!empty($bookingId)) {
            $reservation = $reservationModel->where('bookingID', $bookingId)->first();
            if (!$reservation) {
                // create a reservation record for this booking via model
                $insertData = [
                    'bookingID' => $bookingId,
                    'DownPayment' => 0,
                    'Term_Months' => null,
                    'Monthly_Amortization' => null,
                    // Use a valid enum/status value that exists in the DB schema so inserts succeed
                    'Status' => 'Ongoing',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                // Try inserting using the model/table defined in the model first (usually 'reservations')
                $newId = false;
                try {
                    $tableName = property_exists($reservationModel, 'table') ? $reservationModel->table : 'reservations';
                    $filtered = $this->filterTableFields($tableName, $insertData);
                    if (!empty($filtered)) {
                        $newId = $reservationModel->insert($filtered);
                    }
                } catch (\Throwable $e) {
                    log_message('warning', 'ReservationModel insert failed: ' . $e->getMessage());
                    $newId = false;
                }

                // If model insert didn't work (e.g. DB has legacy table), attempt legacy table names
                if (empty($newId)) {
                    $legacy = ['houserreservation', 'houserReservation', 'houser_reservation'];
                    foreach ($legacy as $t) {
                        try {
                            if ($db->tableExists($t)) {
                                $filtered2 = $this->filterTableFields($t, $insertData);
                                if (!empty($filtered2)) {
                                    $db->table($t)->insert($filtered2);
                                    $newId = $db->insertID();
                                    if (!empty($newId)) break;
                                }
                            }
                        } catch (\Throwable $_e) {
                            // ignore and try next
                        }
                    }
                }

                if ($newId) {
                    // reload reservation from whichever table the ID came from
                    try {
                        $reservation = $reservationModel->find($newId);
                    } catch (\Throwable $_e) {
                        // fallback: try to read from legacy table
                        foreach (['houserreservation','houserReservation','houser_reservation'] as $t) {
                            try {
                                if ($db->tableExists($t)) {
                                    $reservation = $db->table($t)->where('reservationID', $newId)->get()->getRowArray();
                                    if ($reservation) break;
                                }
                            } catch (\Throwable $__e) { }
                        }
                    }
                    $reservationId = $newId;
                }
            } else {
                $reservationId = $reservation['reservationID'] ?? $reservation['ReservationID'] ?? $reservationId;
            }
        }

        if (!$reservation) {
                // Provide diagnostic information about available reservation tables and which fields would be accepted.
                $candidates = ['reservations','houserreservation','houserReservation','houser_reservation'];
                $diag = [];
                foreach ($candidates as $t) {
                    try {
                        $exists = $db->tableExists($t);
                        $filtered = $this->filterTableFields($t, [
                            'bookingID'=> $bookingId,
                            'DownPayment'=>0,
                            'Term_Months'=>null,
                            'Monthly_Amortization'=>null,
                            'Status'=>'PendingConfirmation',
                        ]);
                        $diag[$t] = ['exists' => $exists, 'allowed_keys' => array_values(array_keys($filtered))];
                    } catch (\Throwable $_e) {
                        $diag[$t] = ['exists' => false, 'allowed_keys' => []];
                    }
                }

                return $this->response->setStatusCode(404)->setJSON(['error' => 'Reservation not found or could not be created', 'diagnostic' => $diag]);
        }

        // Check ownership through booking
        $bookingModel = new \App\Models\BookingModel();
        $booking = $bookingModel->find($reservation['bookingID']);
        $bookingUserId = $booking['UserID'] ?? $booking['userID'] ?? null;
        if ($bookingUserId != $session->get('UserID')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'You do not own this reservation']);
        }

        try {
            // Decode base64 signature and save as blob and PNG file for template filling
            $signatureData = base64_decode(preg_replace('#^data:image/[^;]+;base64,#', '', $signature));
            if ($signatureData === false) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid signature format']);
            }

            // Save signature binary to DB as before (filter update keys). Try model first, then legacy tables.
            $reservationModel = new \App\Models\ReservationModel();
            $upd = [
                'Buyer_Signature' => $signatureData,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            try {
                $tableName = property_exists($reservationModel, 'table') ? $reservationModel->table : 'reservations';
                $filteredUpd = $this->filterTableFields($tableName, $upd);
                if (!empty($filteredUpd)) {
                    $reservationModel->update($reservationId, $filteredUpd);
                } else {
                    // try legacy tables
                    $legacy = ['houserreservation','houserReservation','houser_reservation'];
                    foreach ($legacy as $t) {
                        if ($db->tableExists($t)) {
                            $f2 = $this->filterTableFields($t, $upd);
                            if (!empty($f2)) {
                                $db->table($t)->where('reservationID', $reservationId)->update($f2);
                                break;
                            }
                        }
                    }
                }
            } catch (\Throwable $_e) {
                log_message('warning', 'Failed to persist Buyer_Signature: ' . $_e->getMessage());
            }

            // Also save signature PNG to writable/contracts/signatures for FPDI image insertion
            $sigDir = WRITEPATH . 'contracts/signatures/';
            if (!is_dir($sigDir)) mkdir($sigDir, 0755, true);
            $sigFilename = 'signature_res_' . ($reservationId ?? 'anon') . '_' . time() . '.png';
            $sigPath = $sigDir . $sigFilename;
            file_put_contents($sigPath, $signatureData);

            // Prepare data needed by the PDF template (extract client/agent/property similar to generateContractPDF)
            $usersModel = new UsersModel();
            $client = $usersModel->find($booking['UserID'] ?? $booking['userID'] ?? null);

            $propertyModel = new \App\Models\PropertyModel();
            $bookingPropertyId = $booking['PropertyID'] ?? $booking['propertyID'] ?? null;
            $property = $propertyModel->find($bookingPropertyId);

            $agent = null;
            if (!empty($property['agent_assigned'])) {
                $agent = $usersModel->where('UserID', $property['agent_assigned'])->where('Role', 'Agent')->first();
            }

            $propertyLocation = $property['Location'] ?? '';
            $addressParts = explode(',', $propertyLocation);
            $streetAddress = trim($addressParts[0] ?? $propertyLocation);
            $city = trim($addressParts[1] ?? '');
            $stateProvince = trim($addressParts[2] ?? '');
            $postalZip = trim($addressParts[3] ?? '');

            $startDate = date('Y-m-d', strtotime($booking['BookingDate'] ?? $booking['bookingDate'] ?? 'now'));
            $termMonths = $reservation['Term_Months'] ?? 0;
            $terminationDate = date('Y-m-d', strtotime('+' . $termMonths . ' months', strtotime($startDate)));

            // Determine agreed monthly payment and deposit (20% of property price)
            $contractModel = new \App\Models\ContractModel();
            // Prefer contract by bookingID, fallback to latest proposed by this user
            $latestContract = null;
            try {
                if (!empty($booking['bookingID'])) {
                    $latestContract = $contractModel->where('bookingID', $booking['bookingID'])->orderBy('created_at', 'DESC')->first();
                }
            } catch (\Throwable $e) {
                // ignore if contract table missing
            }
            if (empty($latestContract)) {
                try {
                    $latestContract = $contractModel->where('proposedBy', $session->get('UserID'))->orderBy('created_at', 'DESC')->first();
                } catch (\Throwable $e) {
                    $latestContract = null;
                }
            }

            $monthlyFromContract = null;
            if (!empty($latestContract) && isset($latestContract['monthly'])) {
                $monthlyFromContract = floatval($latestContract['monthly']);
            }

            $monthlyAmount = $monthlyFromContract ?? floatval($reservation['Monthly_Amortization'] ?? 0);
            $propertyPriceValue = floatval($property['Price'] ?? 0);
            $downPayment = round($propertyPriceValue * 0.20, 2);

            $templateFields = [
                'teamLeaderFirstName' => $agent ? ($agent['FirstName'] ?? '') : '',
                'teamLeaderLastName' => $agent ? ($agent['LastName'] ?? '') : '',
                'clientFirstName' => $client['FirstName'] ?? '',
                'clientLastName' => $client['LastName'] ?? '',
                'streetAddress' => $streetAddress,
                'city' => $city,
                'stateProvince' => $stateProvince,
                'postalZip' => $postalZip,
                'termOfContract' => $termMonths > 0 ? $termMonths . ' months' : 'Not specified',
                'startDate' => $startDate,
                'termination' => $terminationDate,
                'monthlyPayment' => number_format($monthlyAmount, 2),
                'deposit' => number_format($downPayment, 2),
                'agreedAmount' => number_format($propertyPriceValue, 2),
                'furnishings' => $property['Description'] ?? '',
                'signDay' => date('d'),
                'signMonth' => date('F'),
                'signYear' => date('Y')
            ];

            // Generate PDF from the provided PDF template using FPDI and the saved signature PNG
            $pdfPath = $this->fillTemplateFromData($templateFields, $sigPath);

            // Mark reservation as pending admin confirmation (do not mark Completed) - filter keys
            // Persist the generated contract filename/path on the reservation so the UI can detect it
            try {
                $parsed = parse_url($pdfPath);
                $pdfFilename = basename($parsed['path'] ?? $pdfPath);
                $updateContract = [
                    // include multiple likely column names; filterTableFields will keep only existing ones
                    'contractPDF' => $pdfFilename,
                    'ContractFile' => $pdfFilename,
                    'contract_file' => $pdfFilename,
                    // Do not write an unsupported Status value (e.g. 'PendingConfirmation') because
                    // the DB enum does not contain it. Presence of the contract file itself will
                    // be used by the UI to hide/show Confirm buttons.
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $reservationModel = new \App\Models\ReservationModel();
                $reservationModel->update($reservationId, $this->filterTableFields('reservations', $updateContract));
            } catch (\Throwable $ee) {
                // Non-fatal: log and continue — UI will still get pdf_url in response
                log_message('warning', 'Failed to persist contract filename on reservation: ' . $ee->getMessage());
            }

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
            // Determine monthly payment and deposit (20% of property price). Prefer contract proposal values when available.
            'monthlyPayment' => (function() use ($reservation, $booking, $property) {
                try {
                    $contractModel = new \App\Models\ContractModel();
                    // Prefer contract by bookingID
                    if (!empty($booking['bookingID'])) {
                        $c = $contractModel->where('bookingID', $booking['bookingID'])->orderBy('created_at','DESC')->first();
                        if (!empty($c) && isset($c['monthly'])) return number_format(floatval($c['monthly']), 2);
                    }
                    // fallback to contract proposedBy booking user
                    $userId = $booking['UserID'] ?? $booking['userID'] ?? null;
                    if (!empty($userId)) {
                        $c = $contractModel->where('proposedBy', $userId)->orderBy('created_at','DESC')->first();
                        if (!empty($c) && isset($c['monthly'])) return number_format(floatval($c['monthly']), 2);
                    }
                } catch (\Throwable $e) {
                    // ignore
                }
                return number_format(floatval($reservation['Monthly_Amortization'] ?? 0), 2);
            })(),
            'deposit' => (function() use ($property, $reservation) {
                $price = floatval($property['Price'] ?? 0);
                $down = round($price * 0.20, 2);
                return number_format($down, 2);
            })(),
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

        // Return a controller URL that will stream the PDF securely
        return site_url('users/contractFile/' . rawurlencode($filename));
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

                    // Write agreed property price, monthly payment and deposit using ASCII 'PHP ' to avoid encoding issues
                    $pdf->SetXY(140, 105);
                    $pdf->Cell(50, 5, 'PHP ' . ($data['agreedAmount'] ?? $data['propertyPrice'] ?? ''), 0, 0, 'R');
                    $pdf->SetXY(140, 120);
                    $pdf->Cell(50, 5, 'PHP ' . ($data['monthlyPayment'] ?? ''), 0, 0, 'R');
                    $pdf->SetXY(140, 135);
                    $pdf->Cell(50, 5, 'PHP ' . ($data['deposit'] ?? ''), 0, 0, 'R');

                    // Insert signature image (adjust coordinates/width). Use larger width for clarity.
                    $sigX = 300; $sigY = 20; $sigW = 70;
                    $pdf->Image($sigPath, $sigX, $sigY, $sigW, 0, 'PNG');

                    // Add signed-on date text: "Signed on this {day} day of {month}, {year}"
                    $signedText = 'Signed on this ' . ($data['signDay'] ?? '') . ' day of ' . ($data['signMonth'] ?? '') . ', ' . ($data['signYear'] ?? '');
                    $pdf->SetXY(30, 150);
                    $pdf->MultiCell(140, 5, trim($signedText), 0, 'L');

                    $outDir = WRITEPATH . 'contracts/';
                    if (!is_dir($outDir)) mkdir($outDir, 0755, true);
                    $outFilename = 'contract_filled_' . time() . '_' . ($session->get('UserID') ?? 'anon') . '.pdf';
                    $outPath = $outDir . $outFilename;
                    $pdf->Output($outPath, 'F');

                    $publicUrl = site_url('users/contractFile/' . rawurlencode($outFilename));
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

                    $pdf->SetXY(140, 105);
                    $pdf->Cell(50, 5, 'PHP ' . ($data['agreedAmount'] ?? $data['propertyPrice'] ?? ''), 0, 0, 'R');
                    $pdf->SetXY(140, 120);
                    $pdf->Cell(50, 5, 'PHP ' . ($data['monthlyPayment'] ?? ''), 0, 0, 'R');
                    $pdf->SetXY(140, 135);
                    $pdf->Cell(50, 5, 'PHP ' . ($data['deposit'] ?? ''), 0, 0, 'R');

                    $sigX = 300; $sigY = 20; $sigW = 70;
                    $pdf->Image($sigPath, $sigX, $sigY, $sigW, 0, 'PNG');
                    // Add signed-on date text for the contract
                    $signedText = 'Signed on this ' . ($data['signDay'] ?? '') . ' day of ' . ($data['signMonth'] ?? '') . ', ' . ($data['signYear'] ?? '');
                    $pdf->SetXY(30, 150);
                    $pdf->MultiCell(140, 5, trim($signedText), 0, 'L');

                    $outDir = WRITEPATH . 'contracts/';
                    if (!is_dir($outDir)) mkdir($outDir, 0755, true);
                    $outFilename = 'contract_filled_test_' . time() . '.pdf';
                    $outPath = $outDir . $outFilename;
                    $pdf->Output($outPath, 'F');

                    $publicUrl = site_url('users/contractFile/' . rawurlencode($outFilename));
                    return $this->response->setJSON(['success' => true, 'pdf_url' => $publicUrl, 'sig_path' => $sigPath]);
                } catch (\Throwable $e) {
                    log_message('error', 'fillTemplatePdfNoAuth error: ' . $e->getMessage());
                    return $this->response->setStatusCode(500)->setJSON(['error' => 'PDF generation failed: ' . $e->getMessage()]);
                }
            }

    /**
     * Helper: fill an existing PDF template using FPDI with provided field values and a signature image file.
     * Returns public URL to generated PDF on success, throws on failure.
     */
    private function fillTemplateFromData(array $data, string $signatureFilePath)
    {
        // Locate template
        $preferred = FCPATH . 'assets/PDFContract/Contract-Agreement.pdf';
        $fallback = FCPATH . 'assets/PDFContruct/Contract-Agreement.pdf';
        if (file_exists($preferred)) {
            $template = $preferred;
        } elseif (file_exists($fallback)) {
            $template = $fallback;
        } else {
            throw new \RuntimeException('PDF template not found. Checked: ' . $preferred . ' and ' . $fallback);
        }

        try {
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($template);
            $tpl = $pdf->importPage(1);
            $size = $pdf->getTemplateSize($tpl);
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($tpl);

            $pdf->SetFont('Helvetica', '', 10);
            $pdf->SetTextColor(0,0,0);

            $writeBox = function($x, $y, $w, $lh, $text) use ($pdf) {
                $pdf->SetXY($x, $y);
                $pdf->MultiCell($w, $lh, trim($text), 0, 'L');
            };

            // Map common fields into sensible positions (adjust if your template uses different coords)
            $writeBox(20, 73, 80, 5, ($data['teamLeaderFirstName'] ?? '') );
            $writeBox(65, 73, 80, 5, ($data['teamLeaderLastName'] ?? ''));
            $writeBox(122, 73, 80, 5, ($data['clientFirstName'] ?? '') . ' ');
            $writeBox(168, 73, 80, 5, ($data['clientLastName'] ?? ''));
            $writeBox(20, 98, 120, 5, ($data['streetAddress'] ?? '') );
            $writeBox(65, 98, 60, 5, ('Nasugbo') );
            $writeBox(110, 98, 60, 5, ('Batangas') );
            $writeBox(155, 98, 60, 5, ('4231') );


            $writeBox(70, 110, 100, 5, ('commence of signing'));
            // Start date
            $writeBox(142, 110, 100, 5, ($data['startDate'] ?? ''));

          
            // Right column: agreed amount, monthly payment and deposit
            $pdf->SetXY(175, 120);
            $pdf->Cell(28, 5, 'PHP ' . ($data['agreedAmount'] ?? $data['propertyPrice'] ?? ''), 0, 0, 'R');

            $pdf->SetXY(20, 129);
            $pdf->Cell(28, 5, 'PHP ' . ($data['monthlyPayment'] ?? ''), 0, 0, 'R');

            $pdf->SetXY(140, 129);
            $pdf->Cell(28, 5, 'PHP ' . ($data['deposit'] ?? ''), 0, 0, 'R');
            
            
     
            $signDay = ($data['signDay'] ?? '');
            $signMonth = ($data['signMonth'] ?? '');
            $signYear = ($data['signYear'] ?? '');

            $pdf->SetXY(47, 300);
            $pdf->MultiCell(160, 5, trim($signDay), 0, 'L');

            $pdf->SetXY(82, 300);
            $pdf->MultiCell(160, 5, trim($signMonth), 0, 'L');

            $pdf->SetXY(117, 300);
            $pdf->MultiCell(160, 5, trim($signYear), 0, 'L');

            // Insert signature image if present
            if (!empty($signatureFilePath) && file_exists($signatureFilePath)) {
                $sigX = 150; $sigY = 300; $sigW = 70;
                $pdf->Image($signatureFilePath, $sigX, $sigY, $sigW, 0, 'PNG');
            }
            
            $outDir = WRITEPATH . 'contracts/';
            if (!is_dir($outDir)) mkdir($outDir, 0755, true);
            $outFilename = 'contract_filled_' . time() . '_' . (session()->get('UserID') ?? 'anon') . '.pdf';
            $outPath = $outDir . $outFilename;
            $pdf->Output($outPath, 'F');

            // Return a controller-served URL for secure access to generated PDF
            return site_url('users/contractFile/' . rawurlencode($outFilename));
        } catch (\Throwable $e) {
            log_message('error', 'fillTemplateFromData error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Filter an associative array to only keys that exist as columns in the given DB table.
     * This avoids SQL errors when application code includes timestamps or fields not present.
     */
    private function filterTableFields(string $table, array $data): array
    {
        $db = \Config\Database::connect();
        try {
            $cols = $db->getFieldNames($table);
        } catch (\Throwable $e) {
            // If table not found or error, return empty to avoid accidental inserts
            log_message('warning', 'filterTableFields: failed to read fields for ' . $table . ' - ' . $e->getMessage());
            return [];
        }
        $out = [];
        foreach ($data as $k => $v) {
            if (in_array($k, $cols, true)) {
                $out[$k] = $v;
            }
        }
        return $out;
    }

    /**
     * Serve a generated contract PDF from writable/contracts securely.
     * GET /users/contractFile/{filename}
     */
    public function contractFile($filename = null)
    {
        if (empty($filename)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'filename required']);
        }

        // normalize filename (prevent directory traversal)
        $safe = rawurldecode($filename);
        if (preg_match('#[\\/]|\.\.#', $safe)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'invalid filename']);
        }

        $path = WRITEPATH . 'contracts/' . $safe;
        if (!is_file($path)) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'file not found']);
        }

        // Serve file with correct headers (inline display)
        $this->response->setHeader('Content-Type', 'application/pdf');
        $this->response->setHeader('Content-Disposition', 'inline; filename="' . basename($path) . '"');
        $this->response->setBody(file_get_contents($path));
        return $this->response;
    }


}
