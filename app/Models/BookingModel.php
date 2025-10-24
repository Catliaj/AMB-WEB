<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table            = 'booking';
    protected $primaryKey       = 'bookingID';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'userID', 'propertyID', 'bookingDate', 'status'
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

    public function getTotalBookings()
    {
        return $this->builder()->countAllResults();
    }

    public function getPendingBookings()
    {
        return $this->where('status', 'Pending')->countAllResults();
    }

    public function getTotalBookingByMonth()
    {
        return $this->select("MONTH(bookingDate) AS month, COUNT(*) AS total")
                    ->where('YEAR(bookingDate)', date('Y'))
                    ->groupBy('MONTH(bookingDate)')
                    ->orderBy('MONTH(bookingDate)', 'ASC')
                    ->findAll();
    }
}
