<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\Linebot;

class Manage extends BaseController
{
    public function index()
    {
        
    }

    public function richmenu() 
    {
        $body = view('manage/richmenu');
        $data = [
            'title' => 'Richmenu',
            'body' => $body
        ];

        // $linebot = new Linebot();
        // echo '<pre>';
        // var_dump($linebot->getRichmenuList());
        // echo '</pre>';

        echo view('template',$data);
    }

    public function loadRichmenu() 
    {
        $richmenuModel = new \App\Models\RichmenuModel();
        $data = $richmenuModel->findAll();
        return $this->response->setJSON($data);
    }

    public function createRichmenu() 
    {
        // $linebot = new Linebot();

        // $richmenu = file_get_contents('../ingredients/richmenu/default.json');
        
        // $richmenuId = $linebot->createRichmenu($richmenu);

        // var_dump($richmenuId);

        // if($richmenu) {
        //     $linebot->uploadRichmenuImage($richmenuId, '../ingredients/richmenu/default.jpg');
        // }
    }

    public function syncRichMenu() 
    {
        $linebot = new Linebot();
        $list = $linebot->getRichmenuList();

        if($list)
        {
            $richMenuIds = [];
            $richmenuModel = new \App\Models\RichmenuModel();
            foreach($list['richmenus'] AS $rich) 
            {
                $row = $richmenuModel->where('richmenuId', $rich['richMenuId'])->first();

                if($row) 
                {
                    # Update
                    $richmenuIds[] = $row['richMenuId'];
                } 
                else 
                {
                    # Insert
                    $data = [
                        'richMenuId' => $rich['richMenuId'],
                        'name' => $rich['name'],
                        'data' => json_encode($rich)
                    ];

                    var_dump($data);

                    $richmenuModel->save($data);
                    $richMenuIds[] = $rich['richMenuId'];
                }
            }

            $deleted = [];
            foreach($list['richmenus'] AS $rich)
            {
                if(!in_array($rich['richMenuId'], $richMenuIds))
                {
                    $deleted[] = $rich['richMenuId'];
                }
            }

            if($deleted) {
                $richmenuModel = new \App\Models\RichmenuModel();
                $richmenuModel->delete($deleted);
            }
        }

    }
}