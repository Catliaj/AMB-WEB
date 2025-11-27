<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UsersModel;
use App\Models\CLientGovernmentIDModel;
use App\Models\LocalEmploymentModel;
use App\Models\OFWModel;

class AdminController extends BaseController
{
    public function adminDashboard()
    {
        $session = session();
        if (!$session->get('isLoggedIn') ) {
            return redirect()->to('/'); 
        }

        if ($session->get('role') !== 'Admin') {
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
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/'); // not logged in
        }

         if ($session->get('role') !== 'Admin') {
            return redirect()->to('/'); 
        }

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
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/'); // not logged in
        }

         if ($session->get('role') !== 'Admin') {
            return redirect()->to('/'); 
        }

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
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/'); // not logged in
        }

         if ($session->get('role') !== 'Admin') {
            return redirect()->to('/'); 
        }

        $userModel = new \App\Models\UsersModel();
        $data['users'] = $userModel->getUsersList();

        $govModel = new \App\Models\CLientGovernmentIDModel();
        $localModel = new \App\Models\LocalEmploymentModel();
        $ofwModel = new \App\Models\OFWModel();
        foreach ($data['users'] as &$user) {
            $user['documents'] = [];

            $baseUrl = base_url();
            $employmentStatus = $user['employmentStatus'] ?? 'locallyemployed'; // default if null

            // Fetch from clientGovernmentID
            $govDocs = $govModel->where('UserID', $user['UserID'])->first();
            if ($govDocs) {
                if (!empty($govDocs['Government_ID'])) {
                    $user['documents'][] = ['name' => 'Government ID', 'url' => $baseUrl . 'uploads/' . $employmentStatus . '/' . $govDocs['Government_ID']];
                }
                if (!empty($govDocs['TIN_ID'])) {
                    $user['documents'][] = ['name' => 'TIN ID', 'url' => $baseUrl . 'uploads/' . $employmentStatus . '/' . $govDocs['TIN_ID']];
                }
                if (!empty($govDocs['Selfie_with_ID'])) {
                    $user['documents'][] = ['name' => 'Selfie with ID', 'url' => $baseUrl . 'uploads/' . $employmentStatus . '/' . $govDocs['Selfie_with_ID']];
                }
            }

            // Fetch from localEmployment
            $localDocs = $localModel->where('UserID', $user['UserID'])->first();
            if ($localDocs) {
                if (!empty($localDocs['Id_With_Signature'])) {
                    $user['documents'][] = ['name' => 'ID with Signature', 'url' => $baseUrl . 'uploads/' . $employmentStatus . '/' . $localDocs['Id_With_Signature']];
                }
                if (!empty($localDocs['Payslip'])) {
                    $user['documents'][] = ['name' => 'Payslip', 'url' => $baseUrl . 'uploads/' . $employmentStatus . '/' . $localDocs['Payslip']];
                }
                if (!empty($localDocs['proof_of_billing'])) {
                    $user['documents'][] = ['name' => 'Proof of Billing', 'url' => $baseUrl . 'uploads/' . $employmentStatus . '/' . $localDocs['proof_of_billing']];
                }
            }

            // Fetch from OFW
            $ofwDocs = $ofwModel->where('UserID', $user['UserID'])->first();
            if ($ofwDocs) {
                if (!empty($ofwDocs['Job_Contract'])) {
                    $user['documents'][] = ['name' => 'Job Contract', 'url' => $baseUrl . 'uploads/' . $employmentStatus . '/' . $ofwDocs['Job_Contract']];
                }
                if (!empty($ofwDocs['Passport'])) {
                    $user['documents'][] = ['name' => 'Passport', 'url' => $baseUrl . 'uploads/' . $employmentStatus . '/' . $ofwDocs['Passport']];
                }
                if (!empty($ofwDocs['Official_Identity_Documents'])) {
                    $user['documents'][] = ['name' => 'Official Identity Documents', 'url' => $baseUrl . 'uploads/' . $employmentStatus . '/' . $ofwDocs['Official_Identity_Documents']];
                }
            }
        }

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
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/'); // not logged in
        }

         if ($session->get('role') !== 'Admin') {
            return redirect()->to('/'); 
        }

        $bookingModel = new \App\Models\BookingModel();

        $data = [
            'UserID'        => session()->get('UserID'),
            'email'         => session()->get('inputEmail'),
            'fullname'      => trim(session()->get('FirstName') . ' ' . session()->get('LastName')),
            'currentUserId' => session()->get('UserID'),
            'booking'       => $bookingModel->getBookingWithStatus(),
        ];

        return view('Pages/admin/user-bookings', $data);
    }

    /**
     * Return booking details as JSON for admin UI (GET /admin/booking/{id})
     */
    public function getBookingDetails($id = null)
    {
        $session = session();
        if (!$session->get('isLoggedIn') ) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        if ($session->get('role') !== 'Admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Missing booking id']);
        }

        $bookingModel = new \App\Models\BookingModel();
        $booking = $bookingModel
            ->select('booking.*, property.Title AS PropertyTitle, property.Location AS PropertyLocation, CONCAT(users.FirstName, " ", users.LastName) AS ClientName, users.Email AS ClientEmail, users.phoneNumber AS ClientPhone')
            ->join('property', 'property.PropertyID = booking.PropertyID', 'left')
            ->join('users', 'users.UserID = booking.UserID', 'left')
            ->where('booking.bookingID', $id)
            ->first();

        if (!$booking) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Booking not found']);
        }

        return $this->response->setJSON($booking);
    }

    /**
     * Export filtered reports as CSV
     * GET /admin/reports/export.csv?startDate=...&endDate=...&property=...&agent=...&status=...
     */
    public function exportReportsCsv()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        if ($session->get('role') !== 'Admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $start = $this->request->getGet('startDate');
        $end = $this->request->getGet('endDate');
        $propertyFilter = $this->request->getGet('property');
        $agentFilter = $this->request->getGet('agent');
        $statusFilter = $this->request->getGet('status');

        $bookingModel = new \App\Models\BookingModel();
        $builder = $bookingModel->builder();

        $builder->select('booking.bookingID, property.PropertyID, property.Title AS PropertyTitle, property.Property_Type AS Property_Type, property.Price AS Sales, CONCAT(agent.FirstName, " ", agent.LastName) AS AgentName, booking.status AS BookingStatus, booking.Reason AS Reason, booking.bookingDate, CONCAT(users.FirstName, " ", users.LastName) AS ClientName, users.Email AS ClientEmail, users.phoneNumber AS ClientPhone')
                ->join('property', 'property.PropertyID = booking.PropertyID', 'left')
                ->join('users', 'users.UserID = booking.userID', 'left')
                ->join('users as agent', 'agent.UserID = property.agent_assigned', 'left')
                ->orderBy('booking.bookingDate', 'DESC');

        if (!empty($start)) {
            $builder->where('booking.bookingDate >=', $start);
        }
        if (!empty($end)) {
            $builder->where('booking.bookingDate <=', $end);
        }
        if (!empty($propertyFilter) && strtolower($propertyFilter) !== 'all properties') {
            // allow filter by property type or by title fragment
            $builder->groupStart();
            $builder->like('property.Property_Type', $propertyFilter);
            $builder->orLike('property.Title', $propertyFilter);
            $builder->groupEnd();
        }
        if (!empty($agentFilter) && strtolower($agentFilter) !== 'all agents') {
            if (is_numeric($agentFilter)) {
                $builder->where('property.agent_assigned', (int)$agentFilter);
            } else {
                $builder->where("CONCAT(agent.FirstName, ' ', agent.LastName) =", $agentFilter);
            }
        }
        if (!empty($statusFilter) && strtolower($statusFilter) !== 'all status') {
            $builder->where('booking.status', $statusFilter);
        }

        $rows = $builder->get()->getResultArray();

        // Compute IsSold flag for each row: sold OR (confirmed + reserved)
        foreach ($rows as &$r) {
            $status = isset($r['BookingStatus']) ? strtolower($r['BookingStatus']) : '';
            $reason = isset($r['Reason']) ? strtolower($r['Reason']) : '';
            $r['IsSold'] = ($status === 'sold' || ($status === 'confirmed' && $reason === 'reserved')) ? 1 : 0;
        }

        // Compute IsSold flag for each row: sold OR (confirmed + reserved)
        foreach ($rows as &$r) {
            $status = isset($r['BookingStatus']) ? strtolower($r['BookingStatus']) : '';
            $reason = isset($r['Reason']) ? strtolower($r['Reason']) : '';
            $r['IsSold'] = ($status === 'sold' || ($status === 'confirmed' && $reason === 'reserved')) ? 1 : 0;
        }

        // Build CSV in memory
        $fp = fopen('php://temp', 'r+');
        $headers = ['BookingID', 'PropertyID', 'PropertyTitle', 'PropertyType', 'AgentName', 'ClientName', 'ClientEmail', 'ClientPhone', 'BookingStatus', 'Reason', 'Sales', 'BookingDate', 'IsSold'];
        fputcsv($fp, $headers);

        foreach ($rows as $r) {
            fputcsv($fp, [
                $r['bookingID'] ?? $r['bookingID'] ?? '',
                $r['PropertyID'] ?? '',
                $r['PropertyTitle'] ?? '',
                $r['Property_Type'] ?? '',
                $r['AgentName'] ?? '',
                $r['ClientName'] ?? '',
                $r['ClientEmail'] ?? '',
                $r['ClientPhone'] ?? '',
                $r['BookingStatus'] ?? '',
                $r['Reason'] ?? '',
                isset($r['Sales']) ? $r['Sales'] : '',
                $r['bookingDate'] ?? '',
                isset($r['IsSold']) ? $r['IsSold'] : 0
            ]);
        }

        rewind($fp);
        $csv = stream_get_contents($fp);
        fclose($fp);

        $filename = 'reports-' . date('Ymd-His') . '.csv';
        return $this->response->setHeader('Content-Type', 'text/csv; charset=utf-8')
                              ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                              ->setBody($csv);
    }

    /**
     * Export filtered reports as PDF (requires Dompdf)
     * GET /admin/reports/export.pdf?startDate=...&endDate=...&property=...&agent=...&status=...
     */
    public function exportReportsPdf()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        if ($session->get('role') !== 'Admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        if (!class_exists('\Dompdf\Dompdf')) {
            // Dompdf not installed — ask user to install via composer
            return $this->response->setStatusCode(500)->setBody('Server PDF generator not available. Please install dompdf/dompdf via Composer.');
        }

        $start = $this->request->getGet('startDate');
        $end = $this->request->getGet('endDate');
        $propertyFilter = $this->request->getGet('property');
        $agentFilter = $this->request->getGet('agent');
        $statusFilter = $this->request->getGet('status');

        $bookingModel = new \App\Models\BookingModel();
        $builder = $bookingModel->builder();

        $builder->select('booking.bookingID, property.PropertyID, property.Title AS PropertyTitle, property.Property_Type AS Property_Type, property.Price AS Sales, CONCAT(agent.FirstName, " ", agent.LastName) AS AgentName, booking.status AS BookingStatus, booking.Reason AS Reason, booking.bookingDate, CONCAT(users.FirstName, " ", users.LastName) AS ClientName, users.Email AS ClientEmail, users.phoneNumber AS ClientPhone')
                ->join('property', 'property.PropertyID = booking.PropertyID', 'left')
                ->join('users', 'users.UserID = booking.userID', 'left')
                ->join('users as agent', 'agent.UserID = property.agent_assigned', 'left')
                ->orderBy('booking.bookingDate', 'DESC');

        if (!empty($start)) {
            $builder->where('booking.bookingDate >=', $start);
        }
        if (!empty($end)) {
            $builder->where('booking.bookingDate <=', $end);
        }
        if (!empty($propertyFilter) && strtolower($propertyFilter) !== 'all properties') {
            $builder->groupStart();
            $builder->like('property.Property_Type', $propertyFilter);
            $builder->orLike('property.Title', $propertyFilter);
            $builder->groupEnd();
        }
        if (!empty($agentFilter) && strtolower($agentFilter) !== 'all agents') {
            if (is_numeric($agentFilter)) {
                $builder->where('property.agent_assigned', (int)$agentFilter);
            } else {
                $builder->where("CONCAT(agent.FirstName, ' ', agent.LastName) =", $agentFilter);
            }
        }
        if (!empty($statusFilter) && strtolower($statusFilter) !== 'all status') {
            $builder->where('booking.status', $statusFilter);
        }

        $rows = $builder->get()->getResultArray();

        // Build simple HTML for PDF
        $html = '<h2>Reports Export</h2>';
        $html .= '<table border="1" cellpadding="6" cellspacing="0" width="100%">';
        $html .= '<thead><tr><th>BookingID</th><th>Property</th><th>PropertyType</th><th>Agent</th><th>Client</th><th>Email</th><th>Phone</th><th>Status</th><th>Reason</th><th>Sales</th><th>Date</th><th>Sold</th></tr></thead><tbody>';
        foreach ($rows as $r) {
            $html .= '<tr>';
            $html .= '<td>' . esc($r['bookingID'] ?? '') . '</td>';
            $html .= '<td>' . esc($r['PropertyTitle'] ?? '') . '</td>';
            $html .= '<td>' . esc($r['Property_Type'] ?? '') . '</td>';
            $html .= '<td>' . esc($r['AgentName'] ?? '') . '</td>';
            $html .= '<td>' . esc($r['ClientName'] ?? '') . '</td>';
            $html .= '<td>' . esc($r['ClientEmail'] ?? '') . '</td>';
            $html .= '<td>' . esc($r['ClientPhone'] ?? '') . '</td>';
            $html .= '<td>' . esc($r['BookingStatus'] ?? '') . '</td>';
            $html .= '<td>' . esc($r['Reason'] ?? '') . '</td>';
            $html .= '<td>' . (isset($r['Sales']) ? number_format($r['Sales'], 2) : '') . '</td>';
            $html .= '<td>' . esc($r['bookingDate'] ?? '') . '</td>';
            $html .= '<td>' . ((isset($r['IsSold']) && $r['IsSold']) ? 'Yes' : 'No') . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $pdfOutput = $dompdf->output();

        $filename = 'reports-' . date('Ymd-His') . '.pdf';
        return $this->response->setHeader('Content-Type', 'application/pdf')
                              ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                              ->setBody($pdfOutput);
    }

    /**
     * Return filtered reports as JSON for the admin UI
     * GET /admin/reports/data?startDate=...&endDate=...&property=...&agent=...&status=...
     */
    public function getReportsData()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        if ($session->get('role') !== 'Admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $start = $this->request->getGet('startDate');
        $end = $this->request->getGet('endDate');
        $propertyFilter = $this->request->getGet('property');
        $agentFilter = $this->request->getGet('agent');
        $statusFilter = $this->request->getGet('status');

        $bookingModel = new \App\Models\BookingModel();
        $builder = $bookingModel->builder();

        $builder->select('booking.bookingID, property.PropertyID, property.Title AS PropertyTitle, property.Property_Type AS Property_Type, property.Price AS Sales, CONCAT(agent.FirstName, " ", agent.LastName) AS AgentName, booking.status AS BookingStatus, booking.Reason AS Reason, booking.bookingDate, CONCAT(users.FirstName, " ", users.LastName) AS ClientName, users.Email AS ClientEmail, users.phoneNumber AS ClientPhone')
                ->join('property', 'property.PropertyID = booking.PropertyID', 'left')
                ->join('users', 'users.UserID = booking.userID', 'left')
                ->join('users as agent', 'agent.UserID = property.agent_assigned', 'left')
                ->orderBy('booking.bookingDate', 'DESC');

        if (!empty($start)) {
            $builder->where('booking.bookingDate >=', $start);
        }
        if (!empty($end)) {
            $builder->where('booking.bookingDate <=', $end);
        }
        if (!empty($propertyFilter) && strtolower($propertyFilter) !== 'all properties') {
            $builder->groupStart();
            $builder->like('property.Property_Type', $propertyFilter);
            $builder->orLike('property.Title', $propertyFilter);
            $builder->groupEnd();
        }
        if (!empty($agentFilter) && strtolower($agentFilter) !== 'all agents') {
            if (is_numeric($agentFilter)) {
                $builder->where('property.agent_assigned', (int)$agentFilter);
            } else {
                $builder->where("CONCAT(agent.FirstName, ' ', agent.LastName) =", $agentFilter);
            }
        }
        if (!empty($statusFilter) && strtolower($statusFilter) !== 'all status') {
            $builder->where('booking.status', $statusFilter);
        }

        $rows = $builder->get()->getResultArray();

        // Compute IsSold flag for each row: sold OR (confirmed + reserved)
        foreach ($rows as &$r) {
            $status = isset($r['BookingStatus']) ? strtolower($r['BookingStatus']) : '';
            $reason = isset($r['Reason']) ? strtolower($r['Reason']) : '';
            $r['IsSold'] = ($status === 'sold' || ($status === 'confirmed' && $reason === 'reserved')) ? 1 : 0;
        }

        return $this->response->setJSON($rows);
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

        // If PropertyID is provided, perform an update instead of create
        $providedId = $this->request->getPost('PropertyID');
        if (!empty($providedId)) {
            $propertyID = (int)$providedId;
            // Remove UserID on update to avoid changing owner unintentionally
            unset($data['UserID']);
            $updated = $propertyModel->update($propertyID, $data);
            if ($updated === false) {
                return redirect()->back()->with('error', 'Failed to update property.');
            }
            // Handle any uploaded images for the existing property
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
                            'Image' => $newName
                        ]);
                    }
                }
            }
            return redirect()->to('/admin/ManageProperties')->with('success', 'Property updated successfully!');
        }

        // Otherwise create a new property
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
                            'Image' => $newName
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


    public function storeAgent()
    {
        $userModel = new \App\Models\UsersModel();

        $data = [
            'FirstName' => $this->request->getPost('FirstName'),
            'MiddleName' => $this->request->getPost('MiddleName'),
            'LastName' => $this->request->getPost('LastName'),
            'Birthdate' => $this->request->getPost('Birthdate'),
            'phoneNumber' => $this->request->getPost('PhoneNumber'),
            'Email' => $this->request->getPost('Email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role' => 'Agent',
            'status' => 'Offline',
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $userId = $userModel->insert($data);

        if ($userId) {
            return redirect()->to('/admin/manageUsers')->with('success', 'Agent added successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to add agent.');
        }
    }

    /**
     * Deactivate a user (set status to 'Deactivated')
     * Expected: POST /admin/user/deactivate/{id}
     */
    public function deactivateUser($id = null)
    {
        $userModel = new UsersModel();
        $user = $userModel->find($id);
        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found'])->setStatusCode(404);
        }

        $updated = $userModel->update($id, ['status' => 'Deactivated', 'updated_at' => date('Y-m-d H:i:s')]);

        if ($updated) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'User deactivated']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to update user'])->setStatusCode(500);
    }

    /**
     * Delete a user
     * Expected: DELETE /admin/user/delete/{id}
     */
    public function deleteUser($id = null)
    {
        $userModel = new UsersModel();
        $user = $userModel->find($id);
        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found'])->setStatusCode(404);
        }

        $deleted = $userModel->delete($id);
        if ($deleted) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'User deleted']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to delete user'])->setStatusCode(500);
    }

    /**
     * Reactivate a previously deactivated user
     * Expected: POST /admin/user/reactivate/{id}
     */
    public function reactivateUser($id = null)
    {
        $userModel = new UsersModel();
        $user = $userModel->find($id);
        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found'])->setStatusCode(404);
        }

        $updated = $userModel->update($id, ['status' => 'Offline', 'updated_at' => date('Y-m-d H:i:s')]);

        if ($updated) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'User reactivated']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to reactivate user'])->setStatusCode(500);
    }

    /**
     * Update an existing user (AJAX)
     * Expected: POST /admin/user/update/{id}
     */
    public function updateUser($id = null)
    {
        $userModel = new UsersModel();
        $user = $userModel->find($id);
        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found'])->setStatusCode(404);
        }

        $data = [];
        $post = $this->request->getPost();
        if (isset($post['FirstName'])) $data['FirstName'] = $post['FirstName'];
        if (isset($post['MiddleName'])) $data['MiddleName'] = $post['MiddleName'];
        if (isset($post['LastName'])) $data['LastName'] = $post['LastName'];
        if (isset($post['Birthdate'])) $data['Birthdate'] = $post['Birthdate'];
        if (isset($post['PhoneNumber'])) $data['PhoneNumber'] = $post['PhoneNumber'];
        if (isset($post['Email'])) $data['Email'] = $post['Email'];
        if (!empty($post['Password'])) {
            $data['password'] = password_hash($post['Password'], PASSWORD_BCRYPT);
        }
        $data['updated_at'] = date('Y-m-d H:i:s');

        $updated = $userModel->update($id, $data);

        if ($updated) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'User updated']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to update user'])->setStatusCode(500);
    }
}
