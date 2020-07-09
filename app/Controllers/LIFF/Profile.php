<?php

namespace App\Controllers\LIFF;

use App\Controllers\LineController;
use CodeIgniter\Exceptions\PageNotFoundException;
use ErrorException;

class Profile extends LineController
{
    public function index()
    {
        // Liff Landing
        // Check token
        // Load Form

        //Get Liff ID
        $conf = new \Config\Liff();

        $header_script = view('liff/profile_script', ['liffid'=>$conf->liffid['profile']]);
        $data = [
            'title' => 'My Profile',
            'header' => $header_script,
            'body' => view('liff/liff_landing')
        ];

        echo view('template', $data);
    }

    public function loadform() {
        helper('form');
        // Receive 

        $liff = new \App\Libraries\Liff();
        $profileModel = new \App\Models\ProfileModel();
        $contactModel = new \App\Models\ContactModel();

        $idToken = $this->request->getPost('idToken');
        $response = $liff->verifyIdToken($idToken);
        $isMember = false;

        if($response)
        {
            $contact = $contactModel->where('userId',$response->sub)->first();
            if($contact && $contact['profile_id'])
            {
                // Is Member
                $isMember = true;
                $profile = $profileModel->find($contact['profile_id']);
            }
            else
            {
                // Not Member
                $profile = [
                    'firstname'=>$response->name,
                    'lastname'=>'',
                    'email'=>$response->email
                ];
            }
        }
        else
        {
            // Token invalid
            throw PageNotFoundException::forPageNotFound();
            return;
        }
        

        $viewData = [
            'profile' => $profile,
            'isMember' => $isMember
        ];
        echo view('liff/profile_body', $viewData);
    }

}