<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ChatSessionModel;
use App\Models\PropertyViewLogsModel;
use App\Models\BookingModel;

class AgentController extends BaseController
{
    public function index()
    {
        //
    }

    public function agentDashboard()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/'); // not logged in
        }
         
        if ($session->get('role') !== 'Agent') {
            return redirect()->to('/'); 
        }


        $agentId = session()->get('UserID');
        $chatSessionModel = new ChatSessionModel;
        $propertyViewModel = new PropertyViewLogsModel;
        $totalClientHandle = $chatSessionModel->getUsersHandledByAgent($agentId);
        $getTotalViewsByAgent = $propertyViewModel->getTotalViewsByAgent($agentId);
        $clients = $chatSessionModel->getClientsHandledByAgent($agentId);
        $mostViewed = $propertyViewModel->getMostViewedPropertyByAgent($agentId);
       
      
        return view('Pages/agent/dashboard', [
            'agentID' => $agentId,
            'email' => session()->get('inputEmail'),
            'fullname' => trim(session()->get('FirstName') . ' ' . session()->get('LastName')),
            'currentUserId' => $agentId,
            'otherUser' => null,
            'totalClientHandle' => $totalClientHandle,
            'getTotalViewsByAgent' => $getTotalViewsByAgent,
            'clients' => $clients,
            'mostViewed' => $mostViewed,
        ]);
    }

    public function agentProfile()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/'); // not logged in
        }

         if ($session->get('role') !== 'Agent') {
            return redirect()->to('/'); 
        }


        return view('Pages/agent/view-profile', [
                'UserID' => session()->get('UserID'),
                'email' => session()->get('inputEmail'),
                'fullname' => trim(session()->get('FirstName') . ' ' . session()->
                get('LastName')),
                'currentUserId' => session()->get('UserID'),
                'otherUser' => null
            ]);
    }

    public function agentProperties()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/'); // not logged in
        }

         if ($session->get('role') !== 'Agent') {
            return redirect()->to('/'); 
        }


        $agentID = session()->get('UserID');
        $propertyModel = new \App\Models\PropertyModel();
        $propertyImagesModel = new \App\Models\PropertyImageModel();

        $properties = $propertyModel->getPropertiesByAgents($agentID);
       
        // Attach all images for each property
        foreach ($properties as &$property) {
            $images = $propertyImagesModel
                ->where('PropertyID', $property['PropertyID'])
                ->findAll();

            $property['Images'] = [];

            foreach ($images as $img) {
                $property['Images'][] = [
                    'id' => $img['propertyImageID'] ?? null,
                    'filename' => $img['Image'] ?? null,
                    'url' => base_url('uploads/properties/' . ($img['Image'] ?: 'no-image.jpg'))
                ];
            }

            // fallback if no image exists
            if (empty($property['Images'])) {
                $property['Images'][] = [
                    'id' => null,
                    'filename' => 'no-image.jpg',
                    'url' => base_url('uploads/properties/no-image.jpg')
                ];
            }
        }

        // If AJAX call
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($properties);
        }

        return view('Pages/agent/properties', [
            'UserID' => session()->get('UserID'),
            'email' => session()->get('inputEmail'),
            'fullname' => trim(session()->get('FirstName') . ' ' . session()->get('LastName')),
            
        ]);
    }




    public function agentClients()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/'); // not logged in
        }
         if ($session->get('role') !== 'Agent') {
            return redirect()->to('/'); 
        }


        $chatSessionModel = new ChatSessionModel;
        $agentId = session()->get('UserID');

        $clients = $chatSessionModel->getClientsHandledByAgent($agentId);


        return view('Pages/agent/clients', [
                'UserID' => session()->get('UserID'),
                'email' => session()->get('inputEmail'),
                'fullname' => trim(session()->get('FirstName') . ' ' . session()->
                get('LastName')),
                'currentUserId' => session()->get('UserID'),
                'otherUser' => null,
                'clients' => $clients,
            ]);
    }

    public function agentChat()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/'); // not logged in
        }

         if ($session->get('role') !== 'Agent') {
            return redirect()->to('/'); 
        }


        $session = session();
        $agentId = $session->get('UserID');

        $chatSessionModel = new \App\Models\ChatSessionModel();
        $messageModel = new \App\Models\MessageModel();
        $userModel = new \App\Models\UsersModel();

        // Get all chat sessions for this agent
        $sessions = $chatSessionModel->getSessionsByAgentId($agentId);

        // Prepare user info for sidebar
        $clients = [];
        foreach ($sessions as $s) {
            $user = $userModel->find($s['UserID']);
            if ($user) {
                $clients[] = [
                    'chatSessionID' => $s['chatSessionID'],
                    'fullname' => trim($user['FirstName'] . ' ' . $user['LastName']),
                    'lastMessage' => $messageModel
                        ->where('chatSessionID', $s['chatSessionID'])
                        ->orderBy('timestamp', 'DESC')
                        ->first()['messageText'] ?? 'No messages yet'
                ];
            }
        }

        return view('Pages/agent/chat', [
            'UserID' => $agentId,
            'fullname' => trim($session->get('FirstName') . ' ' . $session->get('LastName')),
            'clients' => $clients,
        ]);
    }


    public function agentBookings()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/'); // not logged in
        }

         if ($session->get('role') !== 'Agent') {
            return redirect()->to('/'); 
        }

        
        $bookingModel = new \App\Models\BookingModel();
        $agentID = session()->get('UserID'); // current agent
        $bookings = $bookingModel->getBookingsByAgent($agentID);

        return view('Pages/agent/bookings', [
                'UserID' => session()->get('UserID'),
                'email' => session()->get('inputEmail'),
                'fullname' => trim(session()->get('FirstName') . ' ' . session()->
                get('LastName')),
                'currentUserId' => session()->get('UserID'),
                'otherUser' => null,
                'bookings' => $bookings,
            ]);
    }

    public function logoutAgent(): ResponseInterface
    {

        $userModel = new \App\Models\UsersModel();
        $userID = session()->get('UserID');

        $userModel->setOnlineToOffline($userID);
        session()->destroy();
        return redirect()->to('/');
    }


    //Nakalimutan ko san to ilalagay
    /*public function updateStatus()
    {
        $propertyID = $this->request->getPost('propertyID');
        $status = $this->request->getPost('status');

        $db = \Config\Database::connect();
        $builder = $db->table('propertyStatusHistory');
        $builder->where('PropertyID', $propertyID);
        $builder->update(['New_Status' => $status]);

        return $this->response->setJSON(['status' => 'success']);
    }*/

    /**
     * API: Return a single booking's data (JSON).
     * GET /users/getBooking/{id}
     */
    public function updateBookingStatus()
        {
            $session = session();
            if (!$session->get('isLoggedIn') || $session->get('role') !== 'Agent') {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
            }

            // Accept JSON body or form POST
            $input = $this->request->getJSON(true);
            if (empty($input)) {
                $input = $this->request->getPost();
            }

            $bookingId = $input['booking_id'] ?? $input['id'] ?? $this->request->getPost('booking_id') ?? null;
            $status = $input['status'] ?? $this->request->getPost('status') ?? null;
            $reason = $input['reason'] ?? $this->request->getPost('reason') ?? null;

            if (empty($bookingId) || empty($status)) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'booking_id and status are required']);
            }

            // Acceptable statuses: Pending, Scheduled, Cancelled, Rejected
            $allowed = [
            'pending'   => 'Pending',
            'scheduled' => 'Scheduled',
            'cancelled' => 'Cancelled',
            'rejected'  => 'Rejected'
            ];
            $statusKey = strtolower(trim($status));
            if (!isset($allowed[$statusKey])) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid status']);
            }
            $normalizedStatus = $allowed[$statusKey];

            $bookingModel = new \App\Models\BookingModel();
            $booking = $bookingModel->find($bookingId);

            if (!$booking) {
                return $this->response->setStatusCode(404)->setJSON(['error' => 'Booking not found']);
            }

            // ownership check if AgentID exists on the booking
            $agentID = session()->get('UserID');
            if (isset($booking['AgentID']) && $booking['AgentID'] != $agentID) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'You do not have permission to modify this booking']);
            }

            // Use direct DB update with raw SQL to ensure PascalCase column names work correctly
            $db = \Config\Database::connect();
            
            // Log the update attempt for debugging
            log_message('debug', "Updating booking {$bookingId} with status: {$normalizedStatus}");
            
            // Build update data
            $updateData = [
                'Status' => $normalizedStatus,
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            if ($reason !== null) {
                $currentNotes = $booking['Notes'] ?? '';
                $updateData['Notes'] = $currentNotes . ($currentNotes ? "\n" : '') . 'Rejection reason: ' . $reason;
            }
            
            // Try using the model first (now that allowedFields is fixed)
            try {
                $updateResult = $bookingModel->update($bookingId, $updateData);
                if ($updateResult !== false) {
                    log_message('debug', "Model update succeeded for booking {$bookingId}");
                    // Verify the update
                    $verifyBooking = $db->table('booking')->select('Status')->where('bookingID', $bookingId)->get()->getRowArray();
                    $verifiedStatus = $verifyBooking['Status'] ?? '';
                    log_message('debug', "Verified status after model update: '{$verifiedStatus}'");
                    return $this->response->setJSON(['success' => true, 'updated' => true, 'status' => $normalizedStatus, 'verified_status' => $verifiedStatus]);
                }
            } catch (\Exception $e) {
                log_message('error', "Model update failed: " . $e->getMessage());
            }
            
            // Fallback to direct DB update using query builder
            $builder = $db->table('booking');
            $builder->set('Status', $normalizedStatus);
            $builder->set('updated_at', date('Y-m-d H:i:s'));
            if ($reason !== null) {
                $currentNotes = $booking['Notes'] ?? '';
                $builder->set('Notes', $currentNotes . ($currentNotes ? "\n" : '') . 'Rejection reason: ' . $reason);
            }
            $builder->where('bookingID', $bookingId);
            $updated = $builder->update();
            
            // Check if update actually affected rows (returns number of affected rows)
            if ($updated === false) {
                log_message('error', "Database update failed for booking {$bookingId}");
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to update booking status - database error']);
            }
            
            if ($updated === 0) {
                log_message('warning', "No rows updated for booking {$bookingId} - booking may not exist or status unchanged");
                // Verify current status
                $currentBooking = $db->table('booking')->select('Status')->where('bookingID', $bookingId)->get()->getRowArray();
                $currentStatus = $currentBooking['Status'] ?? '';
                log_message('debug', "Current status for booking {$bookingId}: '{$currentStatus}', trying to set: '{$normalizedStatus}'");
                if ($currentStatus === $normalizedStatus) {
                    return $this->response->setJSON(['success' => true, 'updated' => true, 'status' => $normalizedStatus, 'message' => 'Status already set']);
                }
                return $this->response->setStatusCode(500)->setJSON(['error' => 'No rows were updated', 'current_status' => $currentStatus]);
            }
            
            log_message('debug', "Successfully updated booking {$bookingId} - {$updated} row(s) affected");
            
            // Verify the update by fetching the booking again
            $verifyBooking = $db->table('booking')->select('Status')->where('bookingID', $bookingId)->get()->getRowArray();
            $verifiedStatus = $verifyBooking['Status'] ?? '';
            log_message('debug', "Verified status after update for booking {$bookingId}: '{$verifiedStatus}'");
            
            return $this->response->setJSON(['success' => true, 'updated' => true, 'status' => $normalizedStatus, 'rows_affected' => $updated, 'verified_status' => $verifiedStatus]);
        }
    public function getBooking($id = null)
    {
        if (empty($id)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Booking id required']);
        }

        $bookingModel = new BookingModel();
        $booking = $bookingModel->find($id);

        if (!$booking) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Booking not found']);
        }

        // Optionally join property or agent info here before returning if you need it on client
        return $this->response->setJSON(['booking' => $booking]);
    }

    /**
     * Return bookings for a given client (userID) — used by agent client details view.
     * GET /users/clientBookings/{userID}
     */
    public function clientBookings($userID = null)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'Agent') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        if (empty($userID)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'UserID required']);
        }

        $bookingModel = new BookingModel();
        try {
            $bookings = $bookingModel->getBookingsByUser($userID);
            return $this->response->setJSON($bookings);
        } catch (\Throwable $e) {
            log_message('error', 'clientBookings error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Server error']);
        }
    }




}
