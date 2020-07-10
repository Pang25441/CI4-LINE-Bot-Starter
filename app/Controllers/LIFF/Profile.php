<?php

namespace App\Controllers\LIFF;

use App\Controllers\LineController;
use CodeIgniter\Exceptions\PageNotFoundException;
use Config\App;
use ErrorException;

class Profile extends LineController
{
    private $profile;

    public function index()
    {
        // Liff Landing
        // Check token
        // Load Form

        //Get Liff ID
        $conf = new \Config\Liff();

        $header_script = view('liff/profile_script', ['liffid' => $conf->liffid['profile']]);
        $data = [
            'title' => 'My Profile',
            'header' => $header_script,
            'body' => view('liff/liff_landing')
        ];

        echo view('template', $data);
    }

    public function loadform()
    {
        helper('form');
        // Receive 

        $profileModel = new \App\Models\ProfileModel();

        $accessToken = $this->request->getPost('accessToken');
        $isMember = false;

        $contact = $contact = $this->getContact($accessToken);
        if ($contact && $contact['profile_id']) {
            // Is Member
            $isMember = true;
            $profileData = $profileModel->find($contact['profile_id']);
        } else {
            // Not Member
            $profileData = [
                'id' => '',
                'firstname' => $this->profile->displayName,
                'lastname' => '',
                'email' => ''
            ];
        }

        $viewData = [
            'profile' => $profileData,
            'isMember' => $isMember
        ];
        echo view('liff/profile_body', $viewData);
    }

    function saveform()
    {
        $contactModel = new \App\Models\ContactModel();
        $profileModel = new \App\Models\ProfileModel();

        $accessToken = $this->request->getPost('accessToken');
        $profileData = $this->request->getPost('profile');

        $contact = $this->getContact($accessToken);

        $profileOrigin = $contact['profile_id'] ? $profileModel->find($contact['profile_id']) : [];

        if($profileOrigin && $profileData['id'] != $contact['profile_id'])
        {
            throw PageNotFoundException::forPageNotFound();
        }

        $profileSave = array_merge($profileOrigin,$profileData);
        $saved = $profileModel->save($profileSave);

        $sendText = false;
        $message = lang("Liff.fail_edit_profile");
        if($saved)
        {
            $message = lang("Liff.finish_edit_profile");
            if(!$profileOrigin){
                $sendText = true;
                $profile_id = $profileModel->getInsertID();
                $message = lang("Liff.finish_new_profile");
                $contact['profile_id'] = $profile_id;
                if(!$contactModel->save($contact))
                {
                    $profileModel->delete($profile_id,true);
                    $saved = false;
                    $message = lang("Liff.fail_new_profile");
                }
            }
        }

        $result = $saved ? true : false;
        $linebot = new \App\Libraries\Linebot();
        if($sendText) $linebot->sendText($contact['userId'], $message);
        return $this->response->setJSON(['result'=>$result, 'message'=>$message]);
    }

    private function getContact($accessToken)
    {
        $liff = new \App\Libraries\Liff();
        $line = new \Config\Line();
        $profileModel = new \App\Models\ProfileModel();
        $contactModel = new \App\Models\ContactModel();

        $response = $liff->verifyAccessToken($accessToken);

        if ($response && $response->client_id == $line->loginChannelId) {
            $this->profile = $liff->getProfile($accessToken);
            return $this->profile ? $contactModel->where('userId', $this->profile->userId)->first() : false;
        } else {
            // Token invalid
            throw PageNotFoundException::forPageNotFound();
            return false;
        }
    }
}
