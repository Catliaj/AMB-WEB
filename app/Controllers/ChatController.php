<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MessageModel;
use Config\Database;

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
        $builder = $db->table('chatSession')->where('chatSessionID', $sessionId);

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
}
