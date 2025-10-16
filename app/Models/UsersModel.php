<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'UserID';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'FirstName', 'MiddleName', 'LastName', 'Birthdate', 'phoneNumber', 'Email', 'Password', 'Role', 'status', 'created_at', 'updated_at'
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


    //assigned Agent Name to Property
    public function getFullname($userID)
    {
        return $this->select('FirstName', 'MiddleName', 'LastName')
                     ->where('UserID', $userID);
    }

   public function getTotalUsers()
    {
        return $this->builder()->countAllResults();
    }

    public function setOfflineToOnline($userID)
    {
        return $this->set('status', 'Online')
                    ->where('UserID', $userID)
                    ->update();
    }


    

}
