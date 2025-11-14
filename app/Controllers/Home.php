<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $session = session();

        if ($session->get('isLoggedIn')) {
            $role = strtolower($session->get('role'));

            switch ($role) {
                case 'admin':
                    return redirect()->to('/admin/adminHomepage');
                case 'agent':
                    return redirect()->to('/users/agentHomepage');
                case 'client':
                    return redirect()->to('/users/clientHomepage');
                default:
                    $session->destroy();
                    return redirect()->to('/');
            }
        }

        return view('Pages/landingpage');
    }
}
