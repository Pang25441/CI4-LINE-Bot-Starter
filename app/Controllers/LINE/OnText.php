<?php

namespace App\Controllers\LINE;

use App\Controllers\LineController;

class OnText extends LineController
{
    public function index()
    {
        $this->linebot->replyText(lang('Line.ThanksForSendMsg'));
    }
}