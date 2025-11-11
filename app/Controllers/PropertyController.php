<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

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
}
