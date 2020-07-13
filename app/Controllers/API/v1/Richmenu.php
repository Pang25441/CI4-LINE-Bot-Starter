<?php

namespace App\Controllers\API\v1;

use App\Controllers\BaseController;

class Richmenu extends BaseController
{

    function setResponse($body, $stausCode = 200)
    {
        $this->response
        ->setStatusCode($stausCode)
        ->setHeader('Access-Control-Allow-Origin', '*')
        ->setHeader('Access-Control-Allow-Headers', '*')
        ->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');

        return $this->response->setJSON($body);
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

        $this->response->setHeader('Access-Control-Allow-Origin', '*')
        ->setHeader('Access-Control-Allow-Headers', '*')
        ->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');   

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

    function create()
    {
        var_dump($this->request->getRawInput());
    }

    function delete($id)
    {

    }

    function setDefault()
    {

    }

    function unsetDefault($id)
    {

    }

}