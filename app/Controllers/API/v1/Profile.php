<?php

namespace App\Controllers\API\v1;

use App\Controllers\BaseController;

class Profile extends BaseController
{

    function setResponse($body, $stausCode = 200)
    {
        $this->response->setHeader('Access-Control-Allow-Origin', '*'); 
        $this->response->setHeader('Access-Control-Allow-Headers', '*');
        $this->response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');

        return $this->response->setStatusCode($stausCode)->setJSON($body);
    }

    function get()
    {
        $profileModel = new \App\Models\ProfileModel();

        $json = $this->request->getJSON();
        $accessToken = $json->accessToken;
        $isMember = false;

        $contact = $contact = $this->getContact($accessToken);

        if ($contact && $contact['profile_id']) 
        {
            # Is Member
            $isMember = true;
            $profileData = $profileModel->find($contact['profile_id']);
        } 
        else 
        {
            # Not Member
            $profileData = [
                'id'        => '',
                'firstname' => $this->profile->displayName,
                'lastname'  => '',
                'email'     => ''
            ];
        }

        $data = [
            'profile'   => $profileData,
            'isMember'  => $isMember
        ];

        return $this->setResponse($data);
    }

    function save()
    {
        $contactModel = new \App\Models\ContactModel();
        $profileModel = new \App\Models\ProfileModel();

        $json = $this->request->getJSON();

        $accessToken = $json->accessToken;
        $profileData = $json->profile;

        $contact = $this->getContact($accessToken);

        $profileOrigin = $contact['profile_id'] ? $profileModel->find($contact['profile_id']) : [];

        // if($profileOrigin && $profileData['id'] != $contact['profile_id'])
        // {
        //     return $this->setResponse(['result'=>false, 'message'=>'x'], 404);
        // }

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
                else
                {
                    $linebot = new \App\Libraries\Linebot();
                    $linebot->linkRichMenuProfile('Member Menu', $profile_id);
                }
            }
        }

        $result = $saved ? true : false;
        $linebot = new \App\Libraries\Linebot();
        if($sendText) $linebot->sendText($contact['userId'], $message);

        return $this->setResponse(['result'=>$result, 'message'=>$message]);
    }

    function qr()
    {
        
    }

    private function getContact($accessToken)
    {
        $liff = new \App\Libraries\Liff();
        $line = new \Config\Line();
        $contactModel = new \App\Models\ContactModel();

        $response = $liff->verifyAccessToken($accessToken);

        if ($response && $response->client_id == $line->loginChannelId) 
        {
            $this->profile = $liff->getProfile($accessToken);
            $contact = $this->profile ? $contactModel->where('userId', $this->profile->userId)->first() : false;

            if($contact)
            {
                if($contact['banned'] == 1)
                {
                    # Banned LINE Account
                    return false;
                }

                # Set Language
                $this->request->setLocale($contact['language']);
            }
            return $contact;
        } 
        else 
        {
            // Token invalid
            return false;
        }
    }
}