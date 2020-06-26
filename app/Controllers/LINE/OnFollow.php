<?php

namespace App\Controllers\LINE;

use App\Controllers\LineController;

class OnFollow extends LineController
{

    public function index()
    {
        echo  'OnFollow';
        $this->replyTextMessage($this->replyToken, lang('Line.Welcome'));

    }
}