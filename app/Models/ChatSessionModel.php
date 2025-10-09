<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatSessionModel extends Model
{
    protected $table            = 'chatsession';
    protected $primaryKey       = 'SessionID';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'UserID', 'AgentID', 'StartTime', 'EndTime', 'Status'
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

    // Get all sessions for a user
    public function getUserSessions($userId)
    {
        $builder = $this->builder();
        $builder->select('chatsession.*, users.FirstName as agentFirstName, users.LastName as agentLastName');
        $builder->join('users', 'users.userID = chatsession.AgentID');
        $builder->where('chatsession.UserID', $userId);
        $builder->where('users.Role', 'Agent');
        $builder->orderBy('chatsession.StartTime', 'DESC');
        $sessions = $builder->get()->getResultArray();

        // Add full agent name
        foreach ($sessions as &$session) {
            $session['agentName'] = trim($session['agentFirstName'] . ' ' . $session['agentLastName']);
        }

        return $sessions;
    }

    // Get all sessions for an agent
    public function getAgentSessions($agentId)
    {
        $builder = $this->builder();
        $builder->select('chatsession.*, users.FirstName as userFirstName, users.LastName as userLastName');
        $builder->join('users', 'users.userID = chatsession.UserID');
        $builder->where('chatsession.AgentID', $agentId);
        $builder->where('users.Role', 'Client');
        $builder->orderBy('chatsession.StartTime', 'DESC');
        $sessions = $builder->get()->getResultArray();

        // Add full user name
        foreach ($sessions as &$session) {
            $session['userName'] = trim($session['userFirstName'] . ' ' . $session['userLastName']);
        }

        return $sessions;
    }

       // ✅ Define custom method
    public function getActiveSessions($userID)
    {
        return $this->where('UserID', $userID)
                    ->where('Status', 'Active')
                    ->findAll();
    }

    public function getFullname($userID)
    {
        return $this->select('',)
                     ->where('UserID', $userID);
    }

    
}
