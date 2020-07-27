<?php

namespace App\Controllers\LINE;

use App\Controllers\LineController;

class OnUnfollow extends LineController
{
    public function index()
    {
        log_message('debug','OnUnfollow');
        
        $contactModel = new \App\Models\ContactModel();
        $contact = $contactModel->where('userId', $this->userId)->first();
        $contact['following'] = 0;
        $contact['unfollow_datetime'] = date('Y-m-d H:i:s');
        $contactModel->save($contact);
    }
}