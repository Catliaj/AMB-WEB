<?php

namespace App\Models;

use CodeIgniter\Model;

class ReservationModel extends Model
{
    protected $table = 'reservations';
    protected $primaryKey = 'reservationID';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
       'bookingID', 'DownPayment', 'Term_Months', 'Monthly_Amortization', 'Buyer_Signature', 'Status', 'contractPDF', 'created_at', 'updated_at'
        
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'bookingID' => 'required|integer',
        'Status' => 'required|in_list[Ongoing,Completed,Defaulted]',
    ];

    protected $validationMessages = [
        'bookingID' => [
            'required' => 'Booking ID is required',
            'integer' => 'Booking ID must be an integer'
        ],
        'Status' => [
            'required' => 'Status is required',
            'in_list' => 'Status must be one of: Ongoing, Completed, Defaulted'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $afterFind = [];
    protected $afterDelete = [];

    /**
     * Get reservations with booking and property details
     */
    public function getReservationsWithDetails($userId = null)
    {
        $builder = $this->select('
            reservations.*,
            reservations.reservationID,
            booking.bookingID,
            booking.BookingDate AS bookingDate,
            booking.Status AS BookingStatus,
            booking.Reason,
            booking.Notes,
            property.PropertyID,
            property.Title AS PropertyTitle,
            property.Description AS PropertyDescription,
            property.Property_Type,
            property.Location AS PropertyLocation,
            property.Size AS PropertySize,
            property.Bedrooms AS PropertyBedrooms,
            property.Bathrooms AS PropertyBathrooms,
            property.Parking_Spaces AS PropertyParking,
            property.agent_assigned,
            property.Corporation,
            property.Price AS PropertyPrice
        ')
        ->join('booking', 'booking.bookingID = reservations.bookingID', 'left')
        ->join('property', 'property.PropertyID = booking.PropertyID', 'left');

        if ($userId) {
            $builder->where('booking.UserID', $userId);
        }

        return $builder->findAll();
    }

    /**
     * Get reservation by booking ID
     */
    public function getByBookingId($bookingId)
    {
        return $this->where('bookingID', $bookingId)->first();
    }
}
