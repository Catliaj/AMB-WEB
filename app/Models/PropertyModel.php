<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyModel extends Model
{
    protected $table            = 'property';
    protected $primaryKey       = 'PropertyID';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'UserID', 'Title', 'Description', 'Property_Type', 'Price', 'Location', 'Size', 'Bedrooms', 'Bathrooms', 'Parking_Spaces','agent_assigned','Corporation'
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


    

    public function getTotalProperties()
    {
        return $this->builder()->countAllResults();
    }

    public function getPropertiesByAgent($agentID)
    {
        return $this->where('agent_assigned', $agentID)->findAll();
    }

    public function getAllProperties()
    {
        return $this->findAll();
    }

    public function getPropertiesWithStatus()
    {
        return $this->select('
                property.*, 
                propertyStatusHistory.New_Status AS New_Status,
                CONCAT(users.FirstName, " ", users.LastName) AS agent_name
            ')
            ->join('propertyStatusHistory', 'propertyStatusHistory.PropertyID = property.PropertyID', 'left')
            ->join('users', 'users.UserID = property.agent_assigned', 'left')
            ->findAll();
    }









}
