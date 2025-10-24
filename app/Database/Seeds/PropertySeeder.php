<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run()
    {
       //'UserID', 'Title', 'Description', 'Property_Type', 'Price', 'Location', 'Size', 'Bedrooms', 'Bathrooms', 'Parking_Spaces','agent_assigned','Corporation'

       
        $data = [
            [
                'UserID' => 1,
                'Title' => 'Modern Apartment in City Center',
                'Description' => 'A beautiful modern apartment located in the heart of the city.',
                'Property_Type' => 'Apartment',
                'Price' => 250000.00,
                'Location' => '123 Main St, Cityville',
                'Size' => 850.5,
                'Bedrooms' => 2,
                'Bathrooms' => 2,
                'Parking_Spaces' => 1,
                'agent_assigned' => 2,
                'Corporation' => 'BellaVita'
            ],
            [
                'UserID' => 1,
                'Title' => 'Cozy Suburban House',
                'Description' => 'A cozy house in a quiet suburban neighborhood.',
                'Property_Type' => 'House',
                'Price' => 350000.00,
                'Location' => '456 Oak St, Suburbia',
                'Size' => 1200.0,
                'Bedrooms' => 3,
                'Bathrooms' => 2,
                'Parking_Spaces' => 2,
                'agent_assigned' => 3,
                'Corporation' => 'BellaVita'
            ],
            [
                'UserID' => 1,
                'Title' => 'Luxury Condo with Ocean View',
                'Description' => 'A luxurious condo offering stunning ocean views.',
                'Property_Type' => 'Condo',
                'Price' => 500000.00,
                'Location' => '789 Beach Ave, Oceanview',
                'Size' => 950.0,
                'Bedrooms' => 2,
                'Bathrooms' => 2,
                'Parking_Spaces' => 1,
                'agent_assigned' => 4,
                'Corporation' => 'BellaVita'
            ],
            [
                'UserID' => 1,
                'Title' => 'Spacious Country Home',
                'Description' => 'A spacious home located in the peaceful countryside.',
                'Property_Type' => 'House',
                'Price' => 400000.00,
                'Location' => '321 Country Rd, Ruralville',
                'Size' => 1500.0,
                'Bedrooms' => 4,
                'Bathrooms' => 3,
                'Parking_Spaces' => 2,
                'agent_assigned' => 5,
                'Corporation' => 'BellaVita'
            ],
            [
                'UserID' => 1,
                'Title' => 'Downtown Loft',
                'Description' => 'A trendy loft located in the downtown area.',
                'Property_Type' => 'Loft',
                'Price' => 300000.00,
                'Location' => '654 Market St, Downtown',
                'Size' => 800.0,
                'Bedrooms' => 1,
                'Bathrooms' => 1,
                'Parking_Spaces' => 0,
                'agent_assigned' => 2,
                'Corporation' => 'BellaVita'
            ],
        ];

        // Using Query Builder
        $this->db->table('property')->insertBatch($data);
    }
}
