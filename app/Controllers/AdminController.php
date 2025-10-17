<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UsersModel;

class AdminController extends BaseController
{
    public function index()
    {
        //

    }

    public function createAgent()
    {
        //
    }

    public function storeAgent()
    {
        //
    }

    public function showUsers($id = null)
    {
        //
    }

    public function editUsers($id = null)
    {
        //
    }

    public function updateUsers($id = null)
    {
        //
    }

    public function deleteUsers($id = null)
    {
        //
    }

    public function manageAgents()
    {
        //
    }

    public function viewReports()
    {
        //
    }

    public function logout(): ResponseInterface
    {
        session()->destroy();
        return redirect()->to('/');
    }

    public function adminDashboard()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'Admin') {
            return redirect()->to('/');
        }

        $adminId = session()->get('UserID');

        // ✅ Fetch total users from database
        $userModel = new \App\Models\UsersModel();
        $propertiesModel = new \App\Models\PropertyModel();
        $bookingModel = new \App\Models\BookingModel();
        $PropertyStatusHistoryModel = new \App\Models\PropertyStatusHistoryModel();

        $totalProperties = $propertiesModel->getTotalProperties();
        $totalUsers = $userModel->getTotalUsers();
        $totalBookings = $bookingModel->getTotalBookings();
        $pendingBookings = $bookingModel->getPendingBookings();

        //Bookings per month
        $getTotalBookingsByMonth = $bookingModel->getTotalBookingByMonth();



        // Date by Satus
        $getTotalSoldProperties = $PropertyStatusHistoryModel->getSoldPerMonth();
        $getTotalAvailableProperties = $PropertyStatusHistoryModel->getTotalAvailablePropetries();
        $getTotalReservedProperties = $PropertyStatusHistoryModel->getTotalReservedProperties();

        //Total by Status
        $TotalSoldProperties = $PropertyStatusHistoryModel->getTotalSoldProperties();
        $totalAvailableProperties = $PropertyStatusHistoryModel->getTotalAvailableProperties();
        $totalReservedProperties = $PropertyStatusHistoryModel->getTotalReservedPropertiesCount();
        $totalCancelledProperties = $PropertyStatusHistoryModel->getTotalCancelledProperties();


       // Prepare data for charts Status
       /*
        $soldData = array_fill(1, 12, 0);
        $availableData = array_fill(1, 12, 0);
        $reservedData = array_fill(1, 12, 0);

  
        foreach ($getTotalSoldProperties as $row) {
            $month = (int)$row['month'];
            $soldData[$month] = (int)$row['total'];
        }

    
        foreach ($getTotalAvailableProperties as $row) {
            $month = (int)$row['month'];
            $availableData[$month] = (int)$row['total'];
        }

      
        foreach ($getTotalReservedProperties as $row) {
            $month = (int)$row['month'];
            $reservedData[$month] = (int)$row['total'];
        }

        $data['soldData'] = json_encode(array_values($soldData));
        $data['availableData'] = json_encode(array_values($availableData));
        $data['reservedData'] = json_encode(array_values($reservedData)); */



        $BookingData = array_fill(1, 12, 0);

        foreach ($getTotalBookingsByMonth as $row) {
            $month = (int)$row['month'];
            $BookingData[$month] = (int)$row['total'];
        }

        $data['BookingData'] = json_encode(array_values($BookingData));

        return view('Pages/admin/dashboard', [
            'UserID' => $adminId,
            'email' => session()->get('inputEmail'),
            'fullname' => trim(session()->get('FirstName') . ' ' . session()->get('LastName')),
            'currentUserId' => $adminId,
            'otherUser' => null,
            'totalUsers' => $totalUsers,
            'totalProperties' => $totalProperties,
            'totalBookings' => $totalBookings,
            'pendingBookings' => $pendingBookings,
            'BookingData' => $data['BookingData'],
            'soldData' => $TotalSoldProperties,
            'availableData' =>  $totalAvailableProperties,
            'reservedData' =>  $totalReservedProperties

        ]);
    }


    public function generateReports()
    {
        return view('Pages/admin/generate-reports', [
                'UserID' => session()->get('UserID'),
                'email' => session()->get('inputEmail'),
                'fullname' => trim(session()->get('FirstName') . ' ' . session()->
                get('LastName')),
                'currentUserId' => session()->get('UserID'),
                'otherUser' => null
            ]);
    }

    public function manageProperties()
    {
        $Property = new \App\Models\PropertyModel();
        $data['properties'] = $Property->getPropertiesWithStatus();
        $db = \Config\Database::connect();
        $data['agents'] = $db->table('users')
                         ->select('UserID, CONCAT(FirstName, " ", LastName) AS full_name')
                         ->where('role', 'Agent')
                         ->get()
                         ->getResultArray();






        return view('Pages/admin/manage-properties', [
                'UserID' => session()->get('UserID'),
                'email' => session()->get('inputEmail'),
                'fullname' => trim(session()->get('FirstName') . ' ' . session()->
                get('LastName')),
                'currentUserId' => session()->get('UserID'),
                'otherUser' => null,
                'properties' => $data['properties'],
                'agents' => $data['agents']
                
            ]);
    }

    public function manageUsers()
    {

        $userModel = new \App\Models\UsersModel();
        $data['users'] = $userModel->getUsersList();

        return view('Pages/admin/manage-users', [
                'UserID' => session()->get('UserID'),
                'email' => session()->get('inputEmail'),
                'fullname' => trim(session()->get('FirstName') . ' ' . session()->
                get('LastName')),
                'currentUserId' => session()->get('UserID'),
                'otherUser' => null,
                'users' => $data['users'],
                


                
            ]);
    }




    public function userBooking()
    {
        return view('Pages/admin/user-bookings', [
                'UserID' => session()->get('UserID'),
                'email' => session()->get('inputEmail'),
                'fullname' => trim(session()->get('FirstName') . ' ' . session()->
                get('LastName')),
                'currentUserId' => session()->get('UserID'),
                'otherUser' => null
               
            ]);
    }

    public function viewChats()
    {
        return view('Pages/admin/view-chats', [
                'UserID' => session()->get('UserID'),
                'email' => session()->get('inputEmail'),
                'fullname' => trim(session()->get('FirstName') . ' ' . session()->
                get('LastName')),
                'currentUserId' => session()->get('UserID'),
                'otherUser' => null
            ]);
    }

    public function logoutAdmin(): ResponseInterface
    {


        $userModel = new \App\Models\UsersModel();
        $adminId = session()->get('UserID');

        $userModel->setOnlineToOffline($adminId);
        session()->destroy();
        return redirect()->to('/');
    }



    

}
