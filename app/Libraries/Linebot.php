<?php
namespace App\Libraries;

use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot\MessageBuilder;

class Linebot
{
    protected $event = null;
	protected $source = null;
	protected $source_type = null;
    protected $replyToken = null;
    
    protected $line;
    protected $bot;
    
    function __construct($event=null)
    {
        if($event){
			$this->event = $event;
			$this->source = $this->event->source;
			$this->source_type = $this->event->source->type;
			$this->replyToken = $this->event->replyToken;
        }
        
        $this->line = new \Config\Line();
        
		$httpClient = new CurlHTTPClient($this->line->channelAccessToken);
        $this->bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $this->line->channelSecret]);
        
    }

    public function pushMessage(array $message) 
    {

	}

    public function replyMessage(array $message) 
    {

	}

    public function sendText($userId=null,$text=null) 
    {
        if(!$text || !$userId) 
        {
			return false;
		}

		$textMessageBuilder = $this->buildTextMessage($text);

		$response = $textMessageBuilder ? $this->bot->pushMessage($userId, $textMessageBuilder) : false;
		
		return $this->reponseHandler('sendTextMessage', $response);
	}

    public function replyText($replyToken=null,$text=null) 
    {
        if(!$text || !$replyToken) 
        {
			return false;
		}
		
        $textMessageBuilder = $this->buildTextMessage($text);
        
        $response = $textMessageBuilder ? $this->bot->replyMessage($replyToken, $textMessageBuilder) : false;
        
		return $this->reponseHandler('replyTextMessage', $response);
	}

    private function buildTextMessage($text) 
    {
        if(is_array($text)) 
        {
            switch(count($text)) 
            {
				case 1:
					$textMessageBuilder = new MessageBuilder\TextMessageBuilder($text[0]);
					break;
				case 2:
					$textMessageBuilder = new MessageBuilder\TextMessageBuilder($text[0], $text[1]);
					break;
				case 3:
					$textMessageBuilder = new MessageBuilder\TextMessageBuilder($text[0], $text[1], $text[2]);
					break;
				case 4:
					$textMessageBuilder = new MessageBuilder\TextMessageBuilder($text[0], $text[1], $text[2], $text[3]);
					break;
				case 5:
					$textMessageBuilder = new MessageBuilder\TextMessageBuilder($text[0], $text[1], $text[2], $text[3], $text[4]);
					break;
				default:
					$textMessageBuilder = null;
			}
        } 
        else 
        {
			$textMessageBuilder = new MessageBuilder\TextMessageBuilder($text);
		}

		return $textMessageBuilder;
	}

	private function reponseHandler($origin_method='', $response) {
		$httpStatusCode = $response->getHTTPstatus();
		
		/**
		 * 200 = Success
		 * 4xx = Do not retry
		 * 500 = LINE Bot error Use request id for retry later
		 */
		if($httpStatusCode == 200) {
			return true;
		}

		$requestId = $response->getHeader('X-Line-Request-Id');
		$body = $response->getJSONDecodedBody();

		$error_msg = "$origin_method (ReqId: $requestId) Failed";
		log_message('error',$error_msg);
		log_message('info', var_export($body, true));

		return false;
	}
}