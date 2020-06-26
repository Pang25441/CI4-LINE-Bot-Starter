<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Exceptions\PageNotFoundException;

use \LINE\LINEBot;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot\MessageBuilder;

use Exception;

class LineController extends Controller {
    
    protected $helpers = [];

	protected $event = null;
	protected $source = null;
	protected $source_type = null;
	protected $replyToken = null;

	protected $trigger = '';
	
	protected $line;
    
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);

		//--------------------------------------------------------------------
		// Preload any models, libraries, etc, here.
		//--------------------------------------------------------------------
		// E.g.:
		// $this->session = \Config\Services::session();
		
		$this->line = new \Config\Line();
        $this->receive_payload();
	}

    private function receive_payload() {
		try {
			$payload = file_get_contents('php://input');
			if(!$payload) {
				throw PageNotFoundException::forPageNotFound();
            }
		} catch(Exception $e) {
			throw PageNotFoundException::forPageNotFound();
		}
        
        
		// Validate Signature
		$channelSecret = $this->line->channelSecret; // Channel secret string
        $httpRequestBody = $payload; // Request body string
        $hash = hash_hmac('sha256', $httpRequestBody, $channelSecret, true);
        $signature = base64_encode($hash);
        
		$line_signature = $this->request->getServer('HTTP_X_LINE_SIGNATURE');
		if(isset($line_signature) && !empty($line_signature) && $signature === $line_signature) 
		{
			// Signature Accepted
		} 
		else 
		{
            // Signature Rejected
            throw PageNotFoundException::forPageNotFound();
		}
		

		log_message('info',$payload);
		$decoded_payload = json_decode($payload);

		if(is_array($decoded_payload->events)){
			$this->event = $decoded_payload->events[0];

			$this->source = $this->event->source;
			$this->source_type = $this->event->source->type;

			$this->replyToken = $this->event->replyToken;
		}
		
		if($this->source_type != 'user') {
			throw PageNotFoundException::forPageNotFound();
		}
	}

	protected function pushMessage() {

	}

	protected function replyMessage() {

	}

	protected function sendTextMessage($userId=null,$text=null) {
		if(!$text || !$userId) {
			return false;
		}

		$httpClient = new CurlHTTPClient($this->line->channelAccessToken);
        $bot = new LINEBot($httpClient, ['channelSecret' => $this->line->channelSecret]);

		$textMessageBuilder = $this->buildMessage($text);

        $response = $textMessageBuilder ? $bot->pushMessage($userId, $textMessageBuilder) : false;
		return $response;
        // echo $response->getHTTPStatus() . ' ' . $response->getRawBody();
	}

	protected function replyTextMessage($replyToken=null,$text=null) {
		if(!$text || !$replyToken) {
			return false;
		}

		$httpClient = new CurlHTTPClient($this->line->channelAccessToken);
		$bot = new LINEBot($httpClient, ['channelSecret' => $this->line->channelSecret]);
		
		$textMessageBuilder = $this->buildMessage($text);

		$response = $textMessageBuilder ? $bot->replyMessage($replyToken, $textMessageBuilder) : false;
		return $response;
	}

	private function buildMessage($text) {
		if(is_array($text)) {
			switch(count($text)) {
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
		} else {
			$textMessageBuilder = new MessageBuilder\TextMessageBuilder($text);
		}

		return $textMessageBuilder;
	}
}