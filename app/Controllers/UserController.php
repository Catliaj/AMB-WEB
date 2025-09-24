<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UsersModel;

class UserController extends BaseController
{
    public function index()
    {
        
    }

   public function StoreUsers()
    {
        $insertUser = new UsersModel();

        // Left side = DB column, Right side = input field from form
        $data = [
            'FirstName'   => $this->request->getPost('inputFirstName'),
            'MiddleName'  => $this->request->getPost('inputMiddleName'),
            'LastName'    => $this->request->getPost('inputLastName'),
            'Birthdate'         => $this->request->getPost('inputBirthdate'),
            'phoneNumber' => $this->request->getPost('inputPhoneNumber'),
            'Email'       => $this->request->getPost('inputEmail'),
            'Password'    => password_hash($this->request->getPost('inputPassword'), PASSWORD_DEFAULT),
            'Role'        => 'Client' // default role for sign-up users
        ];

        $insertUser->insert($data);

        // Redirect back to homepage with success message
        return redirect()->to('/')->with('success', 'User Added Successfully');
    }


    public function login()
    {
        $session = session();
        $model = new UsersModel();

        $Username = $this->request->getPost('inputEmail');
        $Password = $this->request->getPost('inputPassword');

        $user = $model->where('Email', $Username)->first(); // Make sure column name matches DB

        if ($user && password_verify($Password, $user['Password'])) {

            // Store session data
            $session->set([
                'userID'   => $user['userID'],
                'inputEmail' => $user['Email'],
                'role'     => $user['Role'],  // Role must exist in your users table
                'logged_in'=> true
            ]);

            // Redirect based on role
            switch ($user['Role']) {
                case 'Agent':
                    return redirect()->to('/Users/agentDashboard');
                case 'Admin':
                    return redirect()->to('/Users/AdminDashboard');
                default:
                    return redirect()->to('/users/clientHomepage');
            }

        } else {
            $session->setFlashdata('error', 'Wrong username or password.');
            return redirect()->to('/'); // homepage with modal
        }
    }





}
