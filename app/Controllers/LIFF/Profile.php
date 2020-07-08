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

        //Get Liff ID
        $liff = new \Config\Liff();

        $header_script = view('liff/profile_script');
        $data = [
            'title' => 'My Profile',
            'header' => $header_script,
            'body' => view('liff/liff_landing', ['liffid'=>$liff->liffid['profile']])
        ];

        echo view('template', $data);
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