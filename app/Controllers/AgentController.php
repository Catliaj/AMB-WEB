<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AgentController extends BaseController
{
    public function index()
    {
        //
    }

    public function agentDashboard()
    {
         if (!session()->get('isLoggedIn') || session()->get('role') !== 'Agent') {
            return redirect()->to('/');
        }

        $agentId = session()->get('userID');
      
        return view('Pages/agent/dashboard', [
            'agentID' => $agentId,
            'email' => session()->get('inputEmail'),
            'fullname' => trim(session()->get('FirstName') . ' ' . session()->get('LastName')),
            'currentUserId' => $agentId,
            'otherUser' => null
        ]);
    }



}
