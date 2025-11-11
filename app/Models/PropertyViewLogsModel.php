<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyViewLogsModel extends Model
{
    protected $table            = 'propertyviewlogs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'UserID', 'PropertyID', 'created_at'
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

    public function getTotalViewsByAgent($agentId)
    {
        return $this->db->table('propertyviewlogs pv')
                        ->selectCount('pv.id', 'total_views')
                        ->join('property p', 'p.PropertyID = pv.PropertyID')
                        ->where('p.agent_assigned', $agentId)
                        ->get()
                        ->getRow()
                        ->total_views ?? 0;
    }

    public function getMostViewedPropertyByAgent($agentId)
    {
        return $this->db->table('propertyviewlogs pv')
                        ->select('p.PropertyID, p.PropertyName, p.Price, p.Location, COUNT(pv.id) as total_views')
                        ->join('properties p', 'p.PropertyID = pv.PropertyID')
                        ->where('p.agent_assigned', $agentId)
                        ->groupBy('p.PropertyID')
                        ->orderBy('total_views', 'DESC')
                        ->limit(1)
                        ->get()
                        ->getRowArray();
    }







}
