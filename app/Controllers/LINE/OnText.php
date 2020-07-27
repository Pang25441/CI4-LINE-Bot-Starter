<?php

namespace App\Controllers\LINE;

use App\Controllers\LineController;

class OnText extends LineController
{
    public function index()
    {
        log_message('debug','OnText');
        if($this->sourceType == 'user')
        {
            $this->linebot->replyText(lang('Line.ThanksForSendMsg'));
        }
    }
}