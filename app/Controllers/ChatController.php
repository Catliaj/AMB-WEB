<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MessageModel;
use Config\Database;
use App\Models\ChatSessionModel;
use App\Models\PropertyModel;


class ChatController extends BaseController
{

    // Get all messages in one chat session
    public function getMessages($sessionId)
    {
        $db = Database::connect();
        $messageModel = new MessageModel();

        $currentUserId = session()->get('UserID');
        $currentRole = session()->get('role'); // 'User' or 'Agent'

        // Check if this session belongs to the logged-in user/agent
        $builder = $db->table('chatsession')->where('chatSessionID', $sessionId);

        if ($currentRole === 'Agent') {
            $builder->where('AgentID', $currentUserId);
        } else {
            $builder->where('UserID', $currentUserId);
        }

        $session = $builder->get()->getRow();

        if (!$session) {
            return $this->response->setJSON(['error' => 'Unauthorized or invalid session']);
        }

        // Get all messages for this chat session
        $messages = $messageModel->where('chatSessionID', $sessionId)
                                 ->orderBy('timestamp', 'ASC')
                                 ->findAll();

        return $this->response->setJSON($messages);
    }

    // Send a message
    public function sendMessage()
    {
        $messageModel = new MessageModel();

        $data = [
            'chatSessionID' => $this->request->getPost('chatSessionID'),
            'senderRole' => session()->get('role'), // 'User' or 'Agent'
            'messageContent' => $this->request->getPost('messageText'),
            'timestamp' => date('Y-m-d H:i:s')
        ];

        $messageModel->insert($data);

        return $this->response->setJSON(['status' => 'success']);
    }

    public function startSession()
    {
        $session = session();
        $userId = $session->get('UserID');

        if (!$userId) {
            return $this->response->setJSON(['status' => 'error', 'error' => 'User not logged in']);
        }

        $input = $this->request->getJSON(true);
        if (!$input || empty($input['PropertyID'])) {
            return $this->response->setJSON(['status' => 'error', 'error' => 'Invalid request: PropertyID missing']);
        }

        $propertyId = $input['PropertyID'];

        // 🏠 Get property info
        $propertyModel = new PropertyModel();
        $property = $propertyModel->find($propertyId);

        if (!$property) {
            return $this->response->setJSON(['status' => 'error', 'error' => 'Property not found']);
        }

        // 🔑 Use the agent_assigned field
        $agentId = $property['agent_assigned']; // ID of the agent

      
        $usersModel = new \App\Models\UsersModel();
        $agent = $usersModel->find($agentId);

        $agentFullName = $agent 
            ? trim($agent['FirstName'] . ' ' . $agent['LastName']) 
            : 'Agent';
 

        $chatSessionModel = new ChatSessionModel();

        // ✅ Check if session already exists
        $existingSession = $chatSessionModel
            ->where('UserID', $userId)
            ->where('AgentID', $agentId)
            ->first();

        if (!$existingSession) {
            // ✅ Create a new session
            $sessionId = $chatSessionModel->insert([
                'UserID' => $userId,
                'AgentID' => $agentId,
                'startTime' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $sessionId = $existingSession['chatSessionID'];
        }

        // ✅ Auto-send a message
        $messageModel = new MessageModel();
        $messageText = 'Hi! I am interested in your property: ' . ($property['Title'] ?? $property['Property_Title'] ?? 'the property');

        $messageModel->insert([
            'chatSessionID' => $sessionId,
            'senderID'      => $userId,
            'messageContent'   => $messageText,
            'timestamp'    => date('Y-m-d H:i:s')
        ]);


       return $this->response->setJSON([
            'status' => 'success',
            'agentName' => $agentFullName,
            'propertyTitle' => $property['Title'] ?? $property['Property_Title'] ?? 'the property',
            'sessionId' => $sessionId
        ]);

    }




    public function view($sessionId)
    {
        $currentUserId = session()->get('UserID');

        $db = \Config\Database::connect();

        // Fetch session to make sure user/agent can access it
        $session = $db->table('chatsession')
                    ->where('chatSessionID', $sessionId)
                    ->where("(UserID = $currentUserId OR AgentID = $currentUserId)")
                    ->get()->getRowArray();

        if (!$session) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Chat session not found');
        }

        // Fetch all sessions for sidebar
        $clients = $db->table('chatsession')
                    ->join('users', 'chatSession.UserID = users.UserID')
                    ->select('chatsession.chatSessionID, CONCAT(users.FirstName," ",users.LastName) as fullname, "" as lastMessage')
                    ->where('chatsession.UserID', $currentUserId)
                    ->orWhere('chatsession.AgentID', $currentUserId)
                    ->get()->getResultArray();

        return view('chat/chat_view', [
            'clients' => $clients,
            'initialSessionId' => $sessionId
        ]);
    }



}
