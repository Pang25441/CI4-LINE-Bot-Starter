<?php

namespace App\Controllers\LIFF;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class Profile extends BaseController
{
    private $profile;
    private $endpoint;

    function __construct()
    {
        $this->endpoint = site_url('LIFF/Profile');
    }

    public function index()
    {
        $this->Me();
    }

    public function Me()
    {
        $conf = new \Config\Liff();

        # Load LIFF initial script
        # Load My Profile Script
        $header_script = view('liff/liff_script', [ 'liffid' => $conf->liffid['profile'], 'endpoint' => $this->endpoint ]) . view('liff/profile_script');

        # Load My Profile Body
        $data = [
            'title'     => 'My Profile',
            'header'    => $header_script,
            'body'      => view('liff/liff_landing')
        ];

        # Load html structure
        echo view('template', $data);
    }

    public function loadform()
    {
        helper('form');
        $profileModel = new \App\Models\ProfileModel();

        $accessToken = $this->request->getPost('accessToken');
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

        $viewData = [
            'profile'   => $profileData,
            'isMember'  => $isMember
        ];

        # Load profile body
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

    function MyQRCode()
    {
        $conf = new \Config\Liff();

        $header_script = view('liff/liff_script', [ 'liffid' => $conf->liffid['MyQR'], 'endpoint' => $this->endpoint ]);
        $header_script .= view('liff/myqr_script');
        $data = [
            'title'     => 'My QR Code',
            'header'    => $header_script,
            'body'      => view('liff/liff_landing')
        ];

        echo view('template', $data);
    }

    function generateQR()
    {
        helper('filesystem');
        $accessToken = $this->request->getPost('accessToken');
        $reload = $this->request->getPost('reload');

        if(!$accessToken)
        {
            throw PageNotFoundException::forPageNotFound();
        }

        $contact = $this->getContact($accessToken);
        
        $profileModel = new \App\Models\ProfileModel();
        $profile = $profileModel->find($contact['profile_id']);

        if(!$profile)
        {
            throw PageNotFoundException::forPageNotFound();
        }

        $url = "https://chart.googleapis.com/chart?cht=qr&chs=500x500&chld=L|1&chl=";

        $filepath = ROOTPATH . 'public/myqrcode/';
        $filename = $contact['id'] . '-' . $contact['uniqueId'] . '.jpg';

        if(!file_exists($filepath)){ mkdir($filepath); }

        if(!file_exists($filepath.$filename) || $reload=='reload')
        {
            $qr = file_get_contents($url.$contact['uniqueId']);
            write_file($filepath.$filename,$qr);
        }

        $type = pathinfo($filepath.$filename, PATHINFO_EXTENSION);
        $data = file_get_contents($filepath.$filename);

        if(!$data) 
        {
            throw PageNotFoundException::forPageNotFound();
        }

        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $information = $profile['firstname'] . ' ' . $profile['lastname'];

        echo view('liff/myqr_show', [ 'image_data'=>$base64, 'information'=> $information ]);
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
                    throw PageNotFoundException::forPageNotFound();
                }

                # Set Language
                $this->request->setLocale($contact['language']);
            }
            return $contact;
        } 
        else 
        {
            // Token invalid
            throw PageNotFoundException::forPageNotFound();
            return false;
        }
    }
}
