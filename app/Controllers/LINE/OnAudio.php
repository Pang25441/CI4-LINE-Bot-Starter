<?php

namespace App\Controllers\LINE;

use App\Controllers\LineController;
use \LINE\LINEBot;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot\MessageBuilder;

class OnAudio extends LineController
{
    private $conf ; 

    public function index()
    {
        $this->conf = new \Config\Line();

        echo  'OnAudio';
    }
}