<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UsersModel;

class AdminController extends BaseController
{
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
        $usersModel = new \App\Models\UsersModel();
        $data['properties'] = $Property->getPropertiesWithStatus();
        $data['agentss'] = $usersModel->getAllAgents();
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
                'agents' => $data['agents'],
                'agentss' => $data['agentss']
                
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

        $bookingModel = new \App\Models\BookingModel();
        $data['booking'] = $bookingModel->getBookingWithStatus();


        return view('Pages/admin/user-bookings', [
                'UserID' => session()->get('UserID'),
                'email' => session()->get('inputEmail'),
                'fullname' => trim(session()->get('FirstName') . ' ' . session()->
                get('LastName')),
                'currentUserId' => session()->get('UserID'),
                'otherUser' => null,
                'booking' => $data['booking']
               
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


   public function storePropertys()
    {
        $propertyModel = new \App\Models\PropertyModel();
        $propertyImagesModel = new \App\Models\PropertyImageModel();

        $data = [
            'UserID' => session()->get('UserID'),
            'Title' => $this->request->getPost('Title'),
            'Description' => $this->request->getPost('Description'),
            'Property_Type' => $this->request->getPost('Property_Type'),
            'Price' => $this->request->getPost('Price'),
            'Location' => $this->request->getPost('Location'),
            'Size' => $this->request->getPost('Size'),
            'Bedrooms' => $this->request->getPost('Bedrooms'),
            'Bathrooms' => $this->request->getPost('Bathrooms'),
            'Parking_Spaces' => $this->request->getPost('Parking_Spaces'),
            'agent_assigned' => $this->request->getPost('agent_assigned'),
            'Corporation' => $this->request->getPost('Corporation')
        ];

        $propertyID = $propertyModel->createWithStatusAndAgent($data);

        if ($propertyID) {
            $images = $this->request->getFiles();

            if ($images && isset($images['images'])) {
                foreach ($images['images'] as $img) {
                    if ($img->isValid() && !$img->hasMoved()) {
                       
                        $newName = $img->getRandomName();
                        $uploadPath = FCPATH . 'uploads/properties/';

                        
                        if (!is_dir($uploadPath)) {
                            mkdir($uploadPath, 0777, true);
                        }

                        $img->move($uploadPath, $newName);

                       
                        $propertyImagesModel->insert([
                            'PropertyID' => $propertyID,
                            'image' => $newName
                        ]);
                    }
                }
            }
        }
        

        if ($propertyID) {
            return redirect()->to('/admin/ManageProperties')->with('success', 'Property added successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to add property.');
        }
    }


    public function getProperty($id)
    {
        $propertyModel = new \App\Models\PropertyModel();
        $propertyImagesModel = new \App\Models\PropertyImageModel();
        $userModel = new \App\Models\UsersModel();
        $statusModel = new \App\Models\PropertyStatusHistoryModel();

        $property = $propertyModel->find($id);
        if (!$property) return $this->response->setJSON(['error' => 'Property not found']);

        
        $status = $statusModel->where('PropertyID', $id)->orderBy('Date', 'DESC')->first();
        $property['New_Status'] = $status['New_Status'] ?? 'Available';

   
        $agent = $userModel->find($property['agent_assigned']);
        $property['AgentName'] = $agent ? $agent['FirstName'] . ' ' . $agent['LastName'] : 'Unassigned';

       
        $images = $propertyImagesModel->where('PropertyID', $id)->findAll();
        $property['images'] = array_map(function($img) {
            return base_url('/uploads/properties/' . $img['Image']); 
        }, $images);

        return $this->response->setJSON($property);
    }


    public function deleteProperty($id)
    {
        $propertyModel = new \App\Models\PropertyModel();
        $imageModel = new \App\Models\PropertyImageModel();

    
        $property = $propertyModel->find($id);
        if (!$property) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Property not found']);
        }

    
        $images = $imageModel->where('PropertyID', $id)->findAll();

    
        foreach ($images as $img) {
            $filePath = FCPATH . '/uploads/properties/' . $img['Image'];
            if (file_exists($filePath)) {
                unlink($filePath); 
            }
        }
        
        $imageModel->where('PropertyID', $id)->delete();
        $propertyModel->delete($id);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Property deleted successfully']);
    }
}
