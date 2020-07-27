<?php

namespace App\Controllers\API\v1;

use App\Controllers\BaseController;

class Richmenu extends BaseController
{

    function setResponse($body, $stausCode = 200)
    {
        $this->response->setHeader('Access-Control-Allow-Origin', '*'); 
        $this->response->setHeader('Access-Control-Allow-Headers', '*');
        $this->response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');

        return $this->response->setStatusCode($stausCode)->setJSON($body);
    }
    
    function all()
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

        return $this->setResponse($data);
    }

    function get($id)
    {
        $richmenuModel = new \App\Models\RichmenuModel();
        $data = $richmenuModel->find($id);

        if($data)
        {
            return $this->response->setJSON($data);
        }
        else
        {
            return $this->response->setJSON([]);
        }
    }

    function sync()
    {
        $linebot = new \App\Libraries\Linebot();
        $list = $linebot->getRichmenuList();

        if ($list) {
            $richMenuIds = [];
            $richmenuModel = new \App\Models\RichmenuModel();
            $richmenuModel->purgeDeleted();
            foreach ($list['richmenus'] as $rich) {
                $row = $richmenuModel->where('richmenuId', $rich['richMenuId'])->withDeleted()->first();
                
                if ($row) {
                    # Update?
                } else {
                    # Insert
                    $data = [
                        'richMenuId' => $rich['richMenuId'],
                        'name' => $rich['name'],
                        'data' => json_encode($rich)
                    ];

                    $richmenuModel->save($data);
                }

                $richMenuIds[] = $rich['richMenuId'];

                $filepath = ROOTPATH. 'public/richmenu/' . $rich['richMenuId'].'.jpg';
                if(true || !file_exists($filepath))
                {
                    $image = $linebot->downloadRichmenuImage($rich['richMenuId']);
                    if($image)
                    {
                        helper('filesystem');
                        write_file($filepath, $image);
                    }
                }
            }

            $localMenus = [];
            $allMenus = $richmenuModel->withDeleted()->findAll();
            foreach ($allMenus as $rich) {
                $localMenus[] = $rich['richMenuId'];
            }
            $deleted1 = array_diff($richMenuIds,$localMenus);
            $deleted2 = array_diff($localMenus,$richMenuIds);
            $deleted = array_merge($deleted1,$deleted2);
            
            if ($deleted) {
                foreach($deleted AS $filename)
                {
                    $filepath = ROOTPATH . 'public/richmenu/' . $filename.'.jpg';
                    if(file_exists($filepath))
                    unlink($filepath);
                }
                $richmenuModel = new \App\Models\RichmenuModel();
                $richmenuModel->where('richMenuId',$deleted)->delete();
            }

            $defaultRichMenuId = $linebot->getDefaultRichMenuId();
            if($defaultRichMenuId)
            {
                $richmenuModel->set('isDefault',null)->update();
                $richmenuModel->where('richMenuId',$defaultRichMenuId)->set('isDefault',1)->update();
            }

            return $this->all();
        }
    
    }

    function create()
    {
        // log_message('info', var_export($this->request->getRawInput(), true));

        $linebot = new \App\Libraries\Linebot();

        $richmenu_image = $this->request->getFile('richmenuimage');

        $newName = null;

        if ($richmenu_image && $richmenu_image->isValid() && !$richmenu_image->hasMoved()) 
        {
            $newName = $richmenu_image->getRandomName();
            $richmenu_image->move(WRITEPATH . 'uploads', $newName);
        } 
        else 
        {
            return $this->setResponse(['status'=>false, 'message'=>'Rich Menu Image is invalid'],401);
        }

        $richmenu_object = $this->request->getPost('richmenudata');

        log_message('info', var_export($richmenu_object, true));


        $json = json_decode($richmenu_object);
        $richmenuModel = new \App\Models\RichmenuModel();
        $data = $richmenuModel->where('name',$json->name)->first();
        
        if($data) {
            if($newName && file_exists(WRITEPATH. 'uploads/' . $newName))
            {
                unlink(WRITEPATH. 'uploads/' . $newName);
            }
            return $this->setResponse(['status'=>false, 'message'=>'Rich Menu Name is duplicated'],402);
        }

        $richmenuId = $linebot->createRichmenu($richmenu_object);

        if ($richmenuId) 
        {
            $uploaded = $linebot->uploadRichmenuImage($richmenuId, WRITEPATH. 'uploads/' . $newName);
            if ($uploaded) 
            {
                $this->sync();
            } 
            else 
            {
                return $this->setResponse(['status'=>false, 'message'=>'Image upload failed.'],500);
            }
        } 
        else 
        {
            return $this->setResponse(['status'=>false, 'message'=>'Rich Menu Create failed.'],500);
        }

        if($newName && file_exists(WRITEPATH. 'uploads/' . $newName))
        {
            unlink(WRITEPATH. 'uploads/' . $newName);
        }
        
        return $this->setResponse(['status'=>true, 'message'=>'Success'],200);
    }

    function reloadImage()
    {
        $json = $this->request->getJSON();
        $richMenuId = $json->richMenuId;
        $linebot = new \App\Libraries\Linebot();
        $image = $linebot->downloadRichmenuImage($richMenuId);
        if($image)
        {
            helper('filesystem');
            write_file(ROOTPATH. 'public/richmenu/' . $richMenuId.'.jpg', $image);
            return $this->setResponse(['status'=>true, 'data'=> base_url('richmenu/'.$richMenuId.'.jpg')],200);
        } 
        else 
        {
            return $this->setResponse(['status'=>false],404);
        }
    }

    function delete()
    {
        $json = $this->request->getJSON();
        $id = (int)$json->id;
        $richmenuModel = new \App\Models\RichmenuModel();
        $data = $richmenuModel->find($id);
        $status = 200;

        if($data)
        {
            $linebot = new \App\Libraries\Linebot();
            $result = $linebot->deleteRichmenu($data['richMenuId']);
            if($result) 
            {
                unlink(ROOTPATH . 'public/richmenu/' . $data['richMenuId'].'.jpg' );
                $richmenuModel->delete($id);
                $response = [
                    'status' => true,
                    'message' => 'Rich Menu Deleted.'
                ];
            }
            else
            {
                $response = [
                    'status' => false,
                    'message' => 'Rich Menu Delete failed.' 
                ];
                $status = 400;
            }
        }
        else
        {
            $response = [
                'status' => false,
                'message' => 'Rich Menu Not found.'
            ];
            $status = 404;
        }

        return $this->setResponse($response,$status);
    }

    function setDefault()
    {

    }

    function unsetDefault($id)
    {

    }

}