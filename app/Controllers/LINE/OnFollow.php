<?php

namespace App\Controllers\LINE;

use App\Controllers\LineController;

class OnFollow extends LineController
{
    public function index()
    {
        log_message('debug','OnFollow');

        $contact_type = $this->checkContact($this->userId);
        if($contact_type == 'NEW_CONTACT')
        {
            $this->linebot->replyText($this->replyToken, lang('Line.Greeting'));
        } 
        else if($contact_type == 'OLD_CONTACT')
        {
            $contactModel = new \App\Models\ContactModel();
            $contact = $contactModel->where('userId', $this->userId)->first();
            $contact['following'] = 1;
            $contact['follow_datetime'] = date('Y-m-d H:i:s');
            $contactModel->save($contact);
            $this->linebot->replyText($this->replyToken, lang('Line.GreetingAgain'));
        }
    }
}