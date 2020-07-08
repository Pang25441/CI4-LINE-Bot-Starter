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
        foreach($data AS $k=>$rich) 
        {
            if( file_exists( ROOTPATH . 'public/richmenu/' . $rich['richMenuId'].'.jpg' ) )
            {
                $data[$k]['image'] = base_url('richmenu/'.$rich['richMenuId'].'.jpg');
            } else {
                $data[$k]['image'] = null;
            }
        }
        return $this->response->setJSON($data);
    }

    public function createRichmenu()
    {
        $session = session();
        $linebot = new Linebot();

        $richmenu_image = $this->request->getFile('richmenuimage');

        $newName = null;

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
        
        if($data) {
            $session->set('save_status',false);
            $session->set('save_message','Rich Menu Name is duplicated');
            if($newName && file_exists(WRITEPATH. 'uploads/' . $newName))
            {
                unlink(WRITEPATH. 'uploads/' . $newName);
            }
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
        if($newName && file_exists(WRITEPATH. 'uploads/' . $newName))
        {
            unlink(WRITEPATH. 'uploads/' . $newName);
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
                $response = [
                    'save_status' => true,
                    'save_message' => 'Rich Menu Deleted.'
                ];
            }
            else
            {
                $response = [
                    'save_status' => false,
                    'save_message' => 'Rich Menu Delete failed.'
                ];
            }
        }
        else
        {
            $response = [
                'save_status' => false,
                'save_message' => 'Rich Menu Not found.'
            ];
        }

        return $this->response->setJSON($response);
    }

    public function loadImage($richMenuId) {
        $linebot = new Linebot();
        $response = $linebot->downloadRichmenuImage($richMenuId);
        if($response->getHTTPStatus() == 200)
        {
            helper('filesystem');
            write_file(ROOTPATH. 'public/richmenu/' . $richMenuId.'.jpg',$response->getRawBody());
        }
    }

    public function setDefault()
    {
        $id = $this->request->getPost('id');
        $richmenuModel = new \App\Models\RichmenuModel();
        $linebot = new Linebot();

        $richmenu = $richmenuModel->find($id);
        $defaultRichMenuId = $richmenu['richMenuId'];
        log_message('info',$defaultRichMenuId);

        if($linebot->setDefaultRichMenu($richmenu['name']))
        {
            $richmenuModel->set(['isDefault'=>null])->update();
            $richmenuModel->where('richMenuId',$defaultRichMenuId)->set(['isDefault'=>1])->update();
            $response = [
                'save_status' => true,
                'save_message' => 'Default Rich Menu has updated.'
            ];
        }
        else
        {
            $response = [
                'save_status' => false,
                'save_message' => 'Set Default Rich Menu failed.'
            ];
        }

        return $this->response->setJSON($response);
    }

    public function unsetDefault()
    {
        // $id = $this->request->getPost('id');
        $richmenuModel = new \App\Models\RichmenuModel();
        $linebot = new Linebot();

        // $richmenu = $richmenuModel->find($id);
        // $defaultRichMenuId = $richmenu['richMenuId'];

        if( $linebot->cancelDefaultRichMenu() )
        {
            $richmenuModel->set(['isDefault'=>null])->update();
            $response = [
                'save_status' => true,
                'save_message' => 'Default Rich Menu has canceled.'
            ];
        }
        else
        {
            $response = [
                'save_status' => false,
                'save_message' => 'Cancel Default Rich Menu Failed'
            ];
        }

        return $this->response->setJSON($response);
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
            
            if ($deleted) {
                $richmenuModel = new \App\Models\RichmenuModel();
                $richmenuModel->delete($deleted);
            }

            $defaultRichMenuId = $linebot->getDefaultRichMenuId();
            if($defaultRichMenuId)
            {
                $richmenuModel->set('isDefault',null)->update();
                $richmenuModel->where('richMenuId',$defaultRichMenuId)->set('isDefault',1)->update();
            }
        }
    }
}
