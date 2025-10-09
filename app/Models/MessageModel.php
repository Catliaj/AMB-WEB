<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageModel extends Model
{
    protected $table            = 'messages';
    protected $primaryKey       = 'MessageID';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'SessionID', 'SenderID', 'SenderRole', 'Content', 'Timestamp'
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
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];


    // Get all messages in a session
    public function getMessagesBySession($sessionId)
    {
        $builder = $this->builder();
        $builder->select('messages.*, users.FirstName as senderFirstName, users.LastName as senderLastName');
        $builder->join('users', 'users.userID = messages.SenderID');
        $builder->where('messages.SessionID', $sessionId);
        $builder->whereIn('users.Role', ['Agent', 'Client']);
        $builder->orderBy('messages.Timestamp', 'ASC');
        $messages = $builder->get()->getResultArray();

        // Add full sender name
        foreach ($messages as &$message) {
            $message['senderName'] = trim($message['senderFirstName'] . ' ' . $message['senderLastName']);
        }

        return $messages;
    }

    // Add new message
    public function addMessage($data)
    {
        return $this->insert($data);
    }
}
