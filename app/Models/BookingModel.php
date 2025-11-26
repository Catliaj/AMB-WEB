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
        'userID', 'propertyID', 'bookingDate', 'status', 'Reason', 'Notes','created_at', 'updated_at'
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


    public function getBookingWithStatus()
    {
        return $this->select('
                booking.*,
                property.Title AS PropertyTitle,
                CONCAT(users.FirstName, " ", users.LastName) AS ClientName
            ')
            ->join('property', 'property.PropertyID = booking.PropertyID', 'left')
            ->join('users', 'users.UserID = booking.UserID', 'left')
            ->orderBy('booking.BookingDate', 'DESC')
            ->findAll();
    }

    public function getBookingByAssignAgent($agentID)
    {
        return $this->select('
                booking.*,
                property.Title AS PropertyTitle,
                CONCAT(users.FirstName, " ", users.LastName) AS ClientName
            ')
            ->join('property', 'property.PropertyID = booking.PropertyID', 'left')
            ->join('users', 'users.UserID = booking.UserID', 'left')
            ->orderBy('booking.BookingDate', 'DESC')
            ->where('property.agent_assigned', $agentID)
            ->findAll();
    }

    public function getBookingsByAgent($agentID)
    {
        return $this->select('
                booking.bookingID,
                booking.bookingDate,
                booking.status AS BookingStatus,
                users.UserID AS ClientID,
                CONCAT(users.FirstName, " ", users.LastName) AS ClientName,
                users.Email AS ClientEmail,
                users.phoneNumber AS ClientPhone,
                property.PropertyID,
                property.Title AS PropertyTitle,
                property.Location AS PropertyLocation
            ')
            ->join('property', 'property.PropertyID = booking.PropertyID', 'left')
            ->join('users', 'users.UserID = booking.UserID', 'left')
            ->where('property.agent_assigned', $agentID)
            ->orderBy('booking.bookingDate', 'DESC')
            ->findAll();
    }


    public function getBookingsByUser($userID)
    {
        return $this->select('
                booking.bookingID,
                booking.bookingDate,
                booking.status AS BookingStatus,
                property.PropertyID,
                property.Title AS PropertyTitle,
                property.Location AS PropertyLocation
            ')
            ->join('property', 'property.PropertyID = booking.PropertyID', 'left')
            ->where('booking.userID', $userID)
            ->orderBy('booking.bookingDate', 'DESC')
            ->findAll();
    }




}
