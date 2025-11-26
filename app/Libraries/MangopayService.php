<?php namespace App\Libraries;

use MangoPay\MangoPayApi;
use MangoPay\Wallet;
use MangoPay\UserNatural;
use MangoPay\PayIn;
use MangoPay\CardPreAuthorization;

class MangopayService {
    private $api;
    
    public function __construct() {
        $this->api = new MangoPayApi();
        $this->api->Config->ClientId = getenv('MANGOPAY_CLIENT_ID');
        $this->api->Config->ClientPassword = getenv('MANGOPAY_API_KEY');
        $this->api->Config->TemporaryFolder = WRITEPATH . 'mangopay/';
    }

    public function getApi() {
        return $this->api;
    }

    // Example: create a wallet for AMB-WEB
    public function createWallet($ownerId, $currency = 'PHP', $description = 'AMB-WEB Wallet') {
        $wallet = new Wallet();
        $wallet->Owners = [$ownerId];
        $wallet->Currency = $currency;
        $wallet->Description = $description;
        return $this->api->Wallets->Create($wallet);
    }

    // Example: create a natural user (you, AMB-WEB)
    public function createUser($firstName, $lastName, $email, $birthday, $country = 'FR', $nationality = 'FR') {
        $user = new UserNatural();
        $user->FirstName = $firstName;
        $user->LastName = $lastName;
        $user->Email = $email;
        $user->Birthday = $birthday;
        $user->CountryOfResidence = $country;
        $user->Nationality = $nationality;
        $user->Occupation = 'Company';
        $user->IncomeRange = 3;
        return $this->api->Users->Create($user);
    }


}
