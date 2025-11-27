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

        return view('Pages/landing_homepage');
    }

    public function getProperty($id = null)
    {
        // Redirect to admin getProperty if admin, else error
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Property id required']);
        }

        $adminController = new \App\Controllers\AdminController();
        return $adminController->getProperty($id);
    }
}
