<?php

namespace App\Controllers\LINE;

use App\Controllers\LineController;

class OnText extends LineController
{

    public function index()
    {
        $this->replyTextMessage($this->replyToken, lang('Line.Welcome'));
    }
}