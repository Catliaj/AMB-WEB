<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PropertyModel;
use App\Models\PropertyStatusHistoryModel;
use App\Models\PropertyImageModel;

class PropertyController extends BaseController
{
    //para client to 
    public function viewProperty($id)
    {
        $propertyModel = new \App\Models\PropertyModel();
        $propertyImagesModel = new \App\Models\PropertyImageModel();
        $userModel = new \App\Models\UsersModel();
        $statusModel = new \App\Models\PropertyStatusHistoryModel();
        $viewModel = new \App\Models\PropertyViewLogsModel();

        $property = $propertyModel->find($id);
        if (!$property) {
            return $this->response->setJSON(['error' => 'Property not found']);
        }

        // Log property view (once per user per day)
        $userID = session()->get('UserID') ?? null;
        $existing = $userID
            ? $viewModel->where('PropertyID', $id)
                        ->where('UserID', $userID)
                        ->where('DATE(created_at)', date('Y-m-d'))
                        ->first()
            : null;

        if (!$existing) {
            $viewModel->insert([
                'PropertyID' => $id,
                'UserID' => $userID,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        // Total views
        $property['total_views'] = $viewModel->where('PropertyID', $id)->countAllResults();

        // Get latest status
        $status = $statusModel->where('PropertyID', $id)->orderBy('Date', 'DESC')->first();
        $property['New_Status'] = $status['New_Status'] ?? 'Available';

        // Agent info
        $agent = $userModel->find($property['agent_assigned']);
        $property['agent_name'] = $agent ? trim($agent['FirstName'] . ' ' . $agent['LastName']) : 'Unassigned';

        // Images
        $images = $propertyImagesModel->where('PropertyID', $id)->findAll();
        $property['images'] = !empty($images) 
        ? array_map(fn($img) => base_url('uploads/properties/' . $img['Image']), $images)
        : [base_url('uploads/properties/no-image.jpg')];

        return $this->response->setJSON($property);
    }

    public function getAllProperties()
    {
        $propertyModel = new \App\Models\PropertyModel();
        $propertyImageModel = new \App\Models\PropertyImageModel();
        $statusModel = new \App\Models\PropertyStatusHistoryModel();

        // Get all properties that are not sold
        $builder = $propertyModel->builder();
        $builder->select('property.*');
        $builder->join('propertyStatusHistory AS psh', 'psh.PropertyID = property.PropertyID', 'left');
        $builder->where('psh.New_Status !=', 'Sold');
        $properties = $builder->get()->getResultArray();

        foreach ($properties as &$p) {
            // Get the first image for this property
            $image = $propertyImageModel
                ->where('PropertyID', $p['PropertyID'])
                ->orderBy('propertyImageID', 'ASC')
                ->first();

            $p['image'] = ($image && !empty($image['Image']))
                ? base_url('uploads/properties/' . $image['Image'])
                : base_url('uploads/properties/no-image.jpg');

            // Format price
            $p['price'] = '₱' . number_format($p['Price'], 2);

            // Get agent name if assigned
            if (!empty($p['agent_assigned'])) {
                $userModel = new \App\Models\UsersModel();
                $agent = $userModel->find($p['agent_assigned']);
                $p['agent_name'] = $agent
                    ? trim($agent['FirstName'] . ' ' . $agent['LastName'])
                    : null;
            } else {
                $p['agent_name'] = null;
            }

            // Map fields for frontend
            $p['id']       = $p['PropertyID'];
            $p['title']    = $p['Title'];
            $p['type']     = $p['Property_Type'];
            $p['location'] = $p['Location'];
            $p['beds']     = $p['Bedrooms'];
            $p['baths']    = $p['Bathrooms'];
            $p['sqft']     = $p['Size'];
        }

        return $this->response->setJSON($properties);
    }
 public function updateStatus()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        // propertyID required
        $propertyID = $this->request->getPost('propertyID') ?? $this->request->getPost('propertyId') ?? null;
        if (empty($propertyID)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'propertyID is required']);
        }

        $propertyModel = new PropertyModel();
        $property = $propertyModel->find($propertyID);
        if (!$property) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Property not found']);
        }

        // Optional ownership check (agent only)
        $userId = $session->get('UserID');
        if (isset($property['agent_assigned']) && !empty($property['agent_assigned']) && $property['agent_assigned'] != $userId) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'You do not have permission to update this property']);
        }

        // Collect updatable property fields (only those present in PropertyModel->allowedFields)
        $updateData = [];
        $title = $this->request->getPost('title');
        $location = $this->request->getPost('location');
        $price = $this->request->getPost('price');
        $description = $this->request->getPost('description');
        $status = $this->request->getPost('status');

        if ($title !== null) $updateData['Title'] = $title;
        if ($location !== null) $updateData['Location'] = $location;
        if ($price !== null) $updateData['Price'] = $price;
        if ($description !== null) $updateData['Description'] = $description;

        // Update property row if there's data to update
        if (!empty($updateData)) {
            try {
                $propertyModel->update($propertyID, $updateData);
            } catch (\Exception $e) {
                log_message('error', 'Property update failed: ' . $e->getMessage());
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to update property']);
            }
        }

        // If status provided -> insert into property status history table
        if ($status !== null) {
            $oldStatus = $property['New_Status'] ?? $property['status'] ?? '';
            if ($status !== $oldStatus) {
                $statusModel = new PropertyStatusHistoryModel();
                try {
                    $statusModel->insert([
                        'PropertyID' => $propertyID,
                        'Old_Status' => $oldStatus,
                        'New_Status' => $status,
                        'Date'       => date('Y-m-d H:i:s')
                    ]);
                } catch (\Exception $e) {
                    log_message('error', 'Failed to insert propertyStatusHistory: ' . $e->getMessage());
                    // do not fail the whole request for history insertion error
                }
            }
        }

        // Handle uploads: accept single "image" or multiple "images[]"
        $uploadedImageUrls = [];
        $imgModel = new PropertyImageModel();
        $uploadDir = FCPATH . 'uploads/properties/';

        // Ensure upload directory exists
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
                log_message('error', 'Failed to create uploads directory: ' . $uploadDir);
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to create upload directory']);
            }
        }

        // multiple files from input name="images[]"
        $files = $this->request->getFiles();
        // gather either "images" array or single "image"
        if (isset($files['images']) && is_array($files['images'])) {
            foreach ($files['images'] as $file) {
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    try {
                        $newName = $file->getRandomName();
                        $file->move($uploadDir, $newName);
                        $imageUrl = base_url('uploads/properties/' . $newName);
                        $uploadedImageUrls[] = $imageUrl;
                        // insert into gallery table
                        $imgModel->insert([
                            'PropertyID' => $propertyID,
                            'Image' => $newName
                        ]);
                    } catch (\Exception $e) {
                        log_message('error', 'Failed to move/insert image: ' . $e->getMessage());
                        // continue processing other images
                    }
                }
            }
        } elseif (isset($files['image']) && $files['image']->isValid() && !$files['image']->hasMoved()) {
            // single file scenario
            $file = $files['image'];
            try {
                $newName = $file->getRandomName();
                $file->move($uploadDir, $newName);
                $imageUrl = base_url('uploads/properties/' . $newName);
                $uploadedImageUrls[] = $imageUrl;
                $imgModel->insert([
                    'PropertyID' => $propertyID,
                    'Image' => $newName
                ]);
            } catch (\Exception $e) {
                log_message('error', 'Single image upload failed: ' . $e->getMessage());
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to save uploaded image']);
            }
        }

        // Build response
        $result = ['success' => true, 'updated' => true];
        if (!empty($uploadedImageUrls)) $result['imageUrls'] = $uploadedImageUrls;

        return $this->response->setJSON($result);
    }
}
