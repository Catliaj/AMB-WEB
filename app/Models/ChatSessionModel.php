<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatSessionModel extends Model
{
    protected $table            = 'chatSession';
    protected $primaryKey       = 'SessionID';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'chatSessionID', 'UserID', 'AgentID', 'startTime', 'endTime'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];



    public function getSessionsByUserId($userId)
    {
        return $this->where('UserID', $userId)->findAll();
    }


    public function getSessionsByAgentId($agentId)
    {
        return $this->where('AgentID', $agentId)->findAll();
    }

    // --- RETRIEVE MESSAGES FROM SESSION --- //
    public function getMessages($chatSessionID)
    {
        $messageModel = new \App\Models\MessageModel();
        return $messageModel
            ->where('chatSessionID', $chatSessionID)
            ->orderBy('created_at', 'ASC') // sort oldest → newest
            ->findAll();
    }

    // --- RETRIEVE ALL MESSAGES FOR A USER --- //
    public function getAllMessagesByUser($userId)
    {
        $messageModel = new \App\Models\MessageModel();

        return $messageModel
            ->select('messages.*, chatSession.UserID, chatSession.AgentID')
            ->join('chatSession', 'chatSession.chatSessionID = messages.chatSessionID')
            ->where('chatSession.UserID', $userId)
            ->orderBy('messages.created_at', 'ASC')
            ->findAll();
    }

    // --- RETRIEVE ALL MESSAGES FOR AN AGENT --- //
    public function getAllMessagesByAgent($agentId)
    {
        $messageModel = new \App\Models\MessageModel();

        return $messageModel
            ->select('messages.*, chatSession.UserID, chatSession.AgentID')
            ->join('chatSession', 'chatSession.chatSessionID = messages.chatSessionID')
            ->where('chatSession.AgentID', $agentId)
            ->orderBy('messages.created_at', 'ASC')
            ->findAll();
    }    
}
