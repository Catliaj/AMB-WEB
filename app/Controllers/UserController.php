<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsersModel;
use App\Models\ChatSessionModel;
use App\Models\MessageModel;
use App\Models\PropertyModel;
use App\Models\UserTokenModel;
use CodeIgniter\HTTP\ResponseInterface;


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
        $ID  = $model->where('Email', $Username)->findColumn('UserID');
        

        if ($user && password_verify($Password, $user['Password'])) {

            if (isset($user['verified']) && !$user['verified']) {
                return redirect()->to('/')->with('error', 'Please verify your email before logging in.');
            }

     
            $session->set([
                'isLoggedIn'  => true,
                'UserID'      => $user['UserID'],
                'inputEmail'  => $user['Email'],
                'role'        => $user['Role'],
                'FirstName'   => $user['FirstName'],
                'MiddleName'  => $user['MiddleName'],
                'LastName'    => $user['LastName']
            ]);
            $model->setOfflineToOnline($ID);

            // Redirect by role
            switch ($user['Role']) {
                case 'Agent':
                    return redirect()->to('/users/agentHomepage')->with('success', 'Login successful!');
                case 'Admin':
                    return redirect()->to('/admin/adminHomepage')->with('success', 'Login successful!');
                default:
                    return redirect()->to('/users/clientHomepage')->with('success', 'Login successful!');
            }
        }

        $session->setFlashdata('error', 'Wrong username or password.');
        return redirect()->to('/');
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
        return view('Pages/client/homepage', [
                'UserID' => session()->get('UserID'),
                'email' => session()->get('inputEmail'),
                'fullname' => trim(session()->get('FirstName') . ' ' . session()->
                get('LastName')),
                'currentUserId' => session()->get('UserID'),
                'otherUser' => null
            ]);
    }

    public function ClientBrowse()
    {
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
       return view('Pages/client/bookings', [
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
        return view('Pages/client/profile', [
                'UserID' => session()->get('UserID'),
                'email' => session()->get('inputEmail'),
                'fullname' => trim(session()->get('FirstName') . ' ' . session()->
                get('LastName')),
                'currentUserId' => session()->get('UserID'),
                'otherUser' => null
            ]);
    }

      public function cleintChat()
    {
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

 

    



}
