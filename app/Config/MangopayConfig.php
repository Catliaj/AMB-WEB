<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class MangopayConfig extends BaseConfig
{
    public string $clientId;
    public string $apiKey;

    public function __construct()
    {
        $this->clientId = env('mangopay.client_id');
        $this->apiKey   = env('mangopay.api_key');
    }
}
