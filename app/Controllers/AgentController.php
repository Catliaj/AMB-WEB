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
         if (!session()->get('isLoggedIn') || session()->get('role') !== 'Agent') {
            return redirect()->to('/');
        }

        $agentId = session()->get('UserID');
        $chatSessionModel = new ChatSessionModel;
        $propertyViewModel = new PropertyViewLogsModel;
        $totalClientHandle = $chatSessionModel->getUsersHandledByAgent($agentId);
        $getTotalViewsByAgent = $propertyViewModel->getTotalViewsByAgent($agentId);
        
       
      
        return view('Pages/agent/dashboard', [
            'agentID' => $agentId,
            'email' => session()->get('inputEmail'),
            'fullname' => trim(session()->get('FirstName') . ' ' . session()->get('LastName')),
            'currentUserId' => $agentId,
            'otherUser' => null,
            'totalClientHandle' => $totalClientHandle,
            'getTotalViewsByAgent' => $getTotalViewsByAgent,
        ]);
    }

    public function agentProfile()
    {
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
        return view('Pages/agent/clients', [
                'UserID' => session()->get('UserID'),
                'email' => session()->get('inputEmail'),
                'fullname' => trim(session()->get('FirstName') . ' ' . session()->
                get('LastName')),
                'currentUserId' => session()->get('UserID'),
                'otherUser' => null
            ]);
    }

    public function agentChat()
    {
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
        return view('Pages/agent/bookings', [
                'UserID' => session()->get('UserID'),
                'email' => session()->get('inputEmail'),
                'fullname' => trim(session()->get('FirstName') . ' ' . session()->
                get('LastName')),
                'currentUserId' => session()->get('UserID'),
                'otherUser' => null
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

    public function updateStatus()
    {
        $propertyID = $this->request->getPost('propertyID');
        $status = $this->request->getPost('status');

        $db = \Config\Database::connect();
        $builder = $db->table('propertyStatusHistory');
        $builder->where('PropertyID', $propertyID);
        $builder->update(['New_Status' => $status]);

        return $this->response->setJSON(['status' => 'success']);
    }




    



}
