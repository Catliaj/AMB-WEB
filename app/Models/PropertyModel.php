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

    public function createWithStatusAndAgent($data)
    {
        $this->db->transStart();

        if (!empty($data['agent_assigned']) && !is_numeric($data['agent_assigned'])) {
            $userModel = new \App\Models\UsersModel();
            $agent = $userModel->where('Role', 'Agent')
                            ->groupStart()
                            ->like('FirstName', $data['agent_assigned'])
                            ->orLike('LastName', $data['agent_assigned'])
                            ->groupEnd()
                            ->first();
            if ($agent) {
                $data['agent_assigned'] = $agent['UserID']; 
            } else {
                $data['agent_assigned'] = null;
            }
        }

        $this->insert($data);
        $propertyID = $this->getInsertID();
        $statusModel = new \App\Models\PropertyStatusHistoryModel();
        $statusModel->insert([
            'PropertyID' => $propertyID,
            'Old_Status' => 'Available',
            'New_Status' => 'Available',
            'Date'       => date('Y-m-d H:i:s')
        ]);

        $this->db->transComplete();
        return $this->db->transStatus() ? $propertyID : false;
    }


    public function getPropertiesByAgents($agentID)
    {
        return $this->select('
                property.*, 
                propertyImage.Image,
                propertyStatusHistory.New_Status AS New_Status
            ')
            ->join('propertyStatusHistory', 'propertyStatusHistory.PropertyID = property.PropertyID', 'left')
            ->join('propertyImage', 'propertyImage.PropertyID = property.PropertyID', 'left')
            ->join('users', 'users.UserID = property.agent_assigned', 'left')
            ->where('property.agent_assigned', $agentID)
            ->groupBy('property.PropertyID') 
            ->findAll();
    }

}
