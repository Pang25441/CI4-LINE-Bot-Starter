<?php

namespace App\Controllers;

use CodeIgniter\Controller;

use App\Libraries\Linebot;

class LineController extends Controller {
    
    protected $helpers = [];

	protected $event = null;
	protected $source = null;
	protected $source_type = null;
	protected $replyToken = null;

	protected $trigger = '';
	
	protected $line;

	protected $linebot;
    
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);
		
        $this->receivePayload();
	}

    private function receivePayload() {
		$this->line = new \Config\Line();

		$payload = file_get_contents('php://input');
		if($payload) {
			log_message('info',$payload);
			$decoded_payload = json_decode($payload);

			if(is_array($decoded_payload->events)){
				$this->event = $decoded_payload->events[0];

				$this->source = $this->event->source;
				$this->sourceType = $this->event->source->type;

				$this->replyToken = $this->event->replyToken;

				$this->checkContact($this->event->source->userId);
			}
		}

		$this->linebot = new Linebot($this->event);
	}

	private function checkContact($userId=null) {
		$contactModel = new \App\Models\ContactModel();
		$contact = $contactModel->where('userId',$userId)->findAll(1);

		if(!$contact) {
			// utf8_encode 
			$data = [
				'userId' => $userId,
				'uniqueId' => uniqid()
			];
			
			$contactModel->insert($data);
		}
	}
	
}