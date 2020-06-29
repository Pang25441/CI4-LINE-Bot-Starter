<?php

namespace App\Controllers\LIFF;

use App\Controllers\LineController;

class Profile extends LineController
{
    public function index()
    {
        // Liff Landing

        // Check token

        // Load Form
    }

    public function register() {
        // Receive 
        helper('form');
        $profileModel = new \App\Models\ProfileModel();
        $profile = ['firstname'=>'asdasd'];

        $viewData = [
            'profile' => $profile
        ];
        echo view('liff/register', $viewData);
    }

}