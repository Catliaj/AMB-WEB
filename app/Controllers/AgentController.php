<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ChatSessionModel;
use App\Models\PropertyViewLogsModel;

class AgentController extends BaseController
{
    public function index()
    {
        //
    }

    public function agentDashboard()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/'); // not logged in
        }
         
        if ($session->get('role') !== 'Agent') {
            return redirect()->to('/'); 
        }


        $agentId = session()->get('UserID');
        $chatSessionModel = new ChatSessionModel;
        $propertyViewModel = new PropertyViewLogsModel;
        $totalClientHandle = $chatSessionModel->getUsersHandledByAgent($agentId);
        $getTotalViewsByAgent = $propertyViewModel->getTotalViewsByAgent($agentId);
        $clients = $chatSessionModel->getClientsHandledByAgent($agentId);
        $mostViewed = $propertyViewModel->getMostViewedPropertyByAgent($agentId);
       
      
        return view('Pages/agent/dashboard', [
            'agentID' => $agentId,
            'email' => session()->get('inputEmail'),
            'fullname' => trim(session()->get('FirstName') . ' ' . session()->get('LastName')),
            'currentUserId' => $agentId,
            'otherUser' => null,
            'totalClientHandle' => $totalClientHandle,
            'getTotalViewsByAgent' => $getTotalViewsByAgent,
            'clients' => $clients,
            'mostViewed' => $mostViewed,
        ]);
    }

    public function agentProfile()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/'); // not logged in
        }

         if ($session->get('role') !== 'Agent') {
            return redirect()->to('/'); 
        }


        return view('Pages/agent/view-profile', [
                'UserID' => session()->get('UserID'),
                'email' => session()->get('inputEmail'),
                'fullname' => trim(session()->get('FirstName') . ' ' . session()->
                get('LastName')),
                'currentUserId' => session()->get('UserID'),
                'otherUser' => null
            ]);
    }

    public function agentProperties()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/'); // not logged in
        }

         if ($session->get('role') !== 'Agent') {
            return redirect()->to('/'); 
        }


        $agentID = session()->get('UserID');
        $propertyModel = new \App\Models\PropertyModel();
        $propertyImagesModel = new \App\Models\PropertyImageModel();

        $properties = $propertyModel->getPropertiesByAgents($agentID);
       
        // Attach all images for each property
        foreach ($properties as &$property) {
            $images = $propertyImagesModel
                ->where('PropertyID', $property['PropertyID'])
                ->findAll();

            $property['Images'] = [];

            foreach ($images as $img) {
                $property['Images'][] = base_url('uploads/properties/' . ($img['Image'] ?: 'no-image.jpg'));
            }

            // fallback if no image exists
            if (empty($property['Images'])) {
                $property['Images'][] = base_url('uploads/properties/no-image.jpg');
            }
        }

        // If AJAX call
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($properties);
        }

        return view('Pages/agent/properties', [
            'UserID' => session()->get('UserID'),
            'email' => session()->get('inputEmail'),
            'fullname' => trim(session()->get('FirstName') . ' ' . session()->get('LastName')),
            
        ]);
    }




    public function agentClients()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/'); // not logged in
        }
         if ($session->get('role') !== 'Agent') {
            return redirect()->to('/'); 
        }


        $chatSessionModel = new ChatSessionModel;
        $agentId = session()->get('UserID');

        $clients = $chatSessionModel->getClientsHandledByAgent($agentId);


        return view('Pages/agent/clients', [
                'UserID' => session()->get('UserID'),
                'email' => session()->get('inputEmail'),
                'fullname' => trim(session()->get('FirstName') . ' ' . session()->
                get('LastName')),
                'currentUserId' => session()->get('UserID'),
                'otherUser' => null,
                'clients' => $clients,
            ]);
    }

    public function agentChat()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/'); // not logged in
        }

         if ($session->get('role') !== 'Agent') {
            return redirect()->to('/'); 
        }


        $session = session();
        $agentId = $session->get('UserID');

        $chatSessionModel = new \App\Models\ChatSessionModel();
        $messageModel = new \App\Models\MessageModel();
        $userModel = new \App\Models\UsersModel();

        // Get all chat sessions for this agent
        $sessions = $chatSessionModel->getSessionsByAgentId($agentId);

        // Prepare user info for sidebar
        $clients = [];
        foreach ($sessions as $s) {
            $user = $userModel->find($s['UserID']);
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

        return view('Pages/agent/chat', [
            'UserID' => $agentId,
            'fullname' => trim($session->get('FirstName') . ' ' . $session->get('LastName')),
            'clients' => $clients,
        ]);
    }


    public function agentBookings()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/'); // not logged in
        }

         if ($session->get('role') !== 'Agent') {
            return redirect()->to('/'); 
        }

        
        $bookingModel = new \App\Models\BookingModel();
        $agentID = session()->get('UserID'); // current agent
        $bookings = $bookingModel->getBookingsByAgent($agentID);

        return view('Pages/agent/bookings', [
                'UserID' => session()->get('UserID'),
                'email' => session()->get('inputEmail'),
                'fullname' => trim(session()->get('FirstName') . ' ' . session()->
                get('LastName')),
                'currentUserId' => session()->get('UserID'),
                'otherUser' => null,
                'bookings' => $bookings,
            ]);
    }

    public function logoutAgent(): ResponseInterface
    {

        $userModel = new \App\Models\UsersModel();
        $userID = session()->get('UserID');

        $userModel->setOnlineToOffline($userID);
        session()->destroy();
        return redirect()->to('/');
    }


    //Nakalimutan ko san to ilalagay
    /*public function updateStatus()
    {
        $propertyID = $this->request->getPost('propertyID');
        $status = $this->request->getPost('status');

        $db = \Config\Database::connect();
        $builder = $db->table('propertyStatusHistory');
        $builder->where('PropertyID', $propertyID);
        $builder->update(['New_Status' => $status]);

        return $this->response->setJSON(['status' => 'success']);
    }*/

    




    



}
