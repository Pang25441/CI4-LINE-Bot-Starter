<?php

namespace App\Controllers\Manage;

use App\Controllers\BaseController;
use App\Libraries\Linebot;

class Richmenu extends BaseController
{
    public function index($status=null)
    {
        $session = session();
        $save_status = $session->get('save_status');
        $save_message = $session->get('save_message');

        $session->remove('save_status');
        $session->remove('save_message');

        $body = view('manage/richmenu', ['save_status' => $save_status, 'save_message' => $save_message]);
        $data = [
            'title' => 'Richmenu',
            'body' => $body
        ];

        echo view('template', $data);
    }

    public function loadRichmenu()
    {
        $richmenuModel = new \App\Models\RichmenuModel();
        $data = $richmenuModel->findAll();
        return $this->response->setJSON($data);
    }

    public function createRichmenu()
    {
        $session = session();
        $linebot = new Linebot();

        $richmenu_image = $this->request->getFile('richmenuimage');

        if ($richmenu_image && $richmenu_image->isValid() && !$richmenu_image->hasMoved()) 
        {
            $newName = $richmenu_image->getRandomName();
            $richmenu_image->move(WRITEPATH . 'uploads', $newName);
        } 
        else 
        {
            $session->set('save_status',false);
            $session->set('save_message','Rich Menu Image is invalid');
        }

        $richmenu_object = $this->request->getPost('richmenudata');

        $json = json_decode($richmenu_object);
        $richmenuModel = new \App\Models\RichmenuModel();
        $data = $richmenuModel->where('name',$json->name)->first();
        // var_export($data);
        // exit();
        
        if($data) {
            $session->set('save_status',false);
            $session->set('save_message','Rich Menu Name is duplicated');
            $this->response->redirect('/Manage/Richmenu');
            return;
        }

        $richmenuId = $linebot->createRichmenu($richmenu_object);

        if ($richmenuId) 
        {
            $uploaded = $linebot->uploadRichmenuImage($richmenuId, WRITEPATH. 'uploads/' . $newName);
            if ($uploaded) 
            {
                $this->syncRichMenu();
                $session->set('save_status',true);
                $session->set('save_message','Success');
            } 
            else 
            {
                
            $session->set('save_status',false);
            $session->set('save_message','Image upload failed.');
            }
        } 
        else 
        {
            
            $session->set('save_status',false);
            $session->set('save_message','Rich Menu Create failed.');
        }
        $this->response->redirect('/Manage/Richmenu');
    }

    public function deleteRichmenu() 
    {
        $id = $this->request->getPost('id');
        $richmenuModel = new \App\Models\RichmenuModel();
        $data = $richmenuModel->where('id',$id)->first();

        if($data)
        {
            $linebot = new Linebot();
            $result = $linebot->deleteRichmenu($data['richMenuId']);
            if($result) 
            {
                $richmenuModel->delete($id);
                $data = [
                    'save_status' => true,
                    'save_message' => 'Rich Menu Deleted.'
                ];
            }
            else
            {
                $data = [
                    'save_status' => false,
                    'save_message' => 'Rich Menu Delete failed.'
                ];
            }
        }
        else
        {
            $data = [
                'save_status' => false,
                'save_message' => 'Rich Menu Not found.'
            ];
        }

        $this->response->setJSON($data);
    }

    public function syncRichMenu()
    {
        $linebot = new Linebot();
        $list = $linebot->getRichmenuList();

        if ($list) {
            $richMenuIds = [];
            $richmenuModel = new \App\Models\RichmenuModel();
            $richmenuModel->purgeDeleted();
            foreach ($list['richmenus'] as $rich) {
                $row = $richmenuModel->where('richmenuId', $rich['richMenuId'])->withDeleted()->first();
                
                if ($row) {
                    # Update
                    $richMenuIds[] = $row['richMenuId'];
                } else {
                    # Insert
                    $data = [
                        'richMenuId' => $rich['richMenuId'],
                        'name' => $rich['name'],
                        'data' => json_encode($rich)
                    ];

                    // var_dump($data);

                    $richmenuModel->save($data);
                    $richMenuIds[] = $rich['richMenuId'];
                }
            }

            $deleted = [];
            foreach ($list['richmenus'] as $rich) {
                if (!in_array($rich['richMenuId'], $richMenuIds)) {
                    $deleted[] = $rich['richMenuId'];
                }
            }
            var_dump($deleted);
            if ($deleted) {
                $richmenuModel = new \App\Models\RichmenuModel();
                $richmenuModel->delete($deleted);
            }
        }
    }
}
