<?php

namespace App\Controllers\LINE;

use App\Controllers\LineController;

class OnPostback extends LineController
{
    public function index()
    {
        echo  'OnPostback';
    }
}