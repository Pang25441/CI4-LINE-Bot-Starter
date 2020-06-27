<?php namespace Config;

use CodeIgniter\Config\BaseConfig;

class LineRoute extends BaseConfig {

    /**
     * Map webhook event to the Controllers
     * Event/Message Type => Controller Class
     */
    public $map = [
        'message'   => [
            'text'      => 'OnText',
            'image'     => 'OnImage',
            'video'     => 'OnVideo',
            'audio'     => 'OnAudio',
            'file'      => 'OnFile',
            'location'  => 'OnLocation',
            'sticker'   => 'OnSticker',
        ],
        'follow'    => 'OnFollow',
        'unfollow'  => 'OnUnfollow',
        'postback'  => 'OnPostback'
    ];

    /**
     * Allow source type
     * Default: ['user','group','room']
     */
    public $allowSource = ['user'];
}