<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyStatusHistoryModel extends Model
{
    protected $table            = 'propertyStatusHistory';
    protected $primaryKey       = 'propertStatusID';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'PropertyID', 'Old_Status', 'New_Status','Date'
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


   
    public function getSoldPerMonth()
    {
    return $this->select("MONTH(Date) AS month, COUNT(*) AS total")
                ->where('New_Status', 'Sold')
                ->where('YEAR(Date)', date('Y'))
                ->groupBy('MONTH(Date)')
                ->orderBy('MONTH(Date)', 'ASC')
                ->findAll();
    }

    public function getTotalAvailablePropetries()
    {
    return $this->select(" COUNT(*) AS total")
                ->where('New_Status', 'Available')
                ->where('YEAR(Date)', date('Y'))
                ->groupBy('MONTH(Date)')
                ->orderBy('MONTH(Date)', 'ASC')
                ->findAll();
    }

    
    public function getTotalReservedProperties()
    {
    return $this->select(" COUNT(*) AS total")
                ->where('New_Status', 'Reserved')
                ->where('YEAR(Date)', date('Y'))
                ->groupBy('MONTH(Date)')
                ->orderBy('MONTH(Date)', 'ASC')
                ->findAll();
    }


    public  function getTotalSoldProperties()
    {
        return $this->where('New_Status', 'Sold')->countAllResults();
    }

    public function getTotalAvailableProperties()
    {
        return $this->where('New_Status', 'Available')->countAllResults();
    }

    public function getTotalReservedPropertiesCount()
    {
        return $this->where('New_Status', 'Reserved')->countAllResults();
    }

    public function getTotalCancelledProperties()
    {
        return $this->where('New_Status', 'Cancelled')->countAllResults();
    }

    public function findPropertiesByID($properttID)
    {
        //get the status history of a property by its ID
        return $this->where('PropertyID', $properttID)->findAll();
    }





}
