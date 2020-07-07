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

	function __construct($event = null)
	{
		if ($event) {
			$this->event = $event;
			$this->source = $this->event->source;
			$this->source_type = $this->event->source->type;
			$this->replyToken = $this->event->replyToken;
		}

		$this->line = new \Config\Line();

		$httpClient = new CurlHTTPClient($this->line->channelAccessToken);
		$this->bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $this->line->channelSecret]);
	}

	public function getProfile($userId)
	{
		$response = $this->bot->getProfile($userId);

		$profile = $response->getJSONDecodedBody();
		// echo $profile['displayName'];
		// echo $profile['pictureUrl'];
		// echo $profile['statusMessage'];

		if ($response->isSucceeded()) {
			return $profile;
		} else {
			log_message('info', var_export($profile, true));
			return false;
		}
	}

	public function pushMessage(array $message)
	{
	}

	public function replyMessage(array $message)
	{
	}

	public function sendText($userId = null, $text = null)
	{
		if (!$text || !$userId) {
			return false;
		}

		$textMessageBuilder = $this->buildTextMessage($text);

		$response = $textMessageBuilder ? $this->bot->pushMessage($userId, $textMessageBuilder) : false;

		return $this->responseHandler('sendTextMessage', $response);
	}

	public function replyText($replyToken = null, $text = null)
	{
		if (!$text || !$replyToken) {
			return false;
		}

		$textMessageBuilder = $this->buildTextMessage($text);

		$response = $textMessageBuilder ? $this->bot->replyMessage($replyToken, $textMessageBuilder) : false;

		return $this->responseHandler('replyTextMessage', $response);
	}

	private function buildTextMessage($text)
	{
		if (is_array($text)) {
			switch (count($text)) {
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


	public function getRichmenuList()
	{
		$response = $this->bot->getRichMenuList();

		if ($this->responseHandler('getRichmenuList', $response)) {
			return $response->getJSONDecodedBody();
		} else {
			return false;
		}
	}

	public function getRichmenuImage($richMenuId)
	{
		$response = $this->bot->downloadRichMenuImage($richMenuId);

		if ($this->responseHandler('getRichmenuList', $response)) {
			return $response->getJSONDecodedBody();
		} else {
			return false;
		}
	}

	public function createRichmenu($richMenuJSON)
	{
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api.line.me/v2/bot/richmenu',
			CURLOPT_RETURNTRANSFER => true,
			// CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $richMenuJSON,
			CURLOPT_HTTPHEADER => array(
				"authorization: Bearer " . $this->line->channelAccessToken,
				"cache-control: no-cache",
				"content-type: application/json"
			),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);
		$status = curl_getinfo($curl);

		$http_code = $status['http_code'];

		curl_close($curl);

		if ($http_code !== 200) 
		{
			log_message('error', $err);
			log_message('error', $response);
			return false;
		} 
		else 
		{
			$_response = json_decode($response);
			return $_response->richMenuId;
		}
	}

	function uploadRichmenuImage($richMenuId, $imagePath) 
	{
		$contentType = mime_content_type($imagePath);
		if(!in_array($contentType, ['image/jpeg', 'image/png']))
		{
			log_message('error','Rich Menu Image Type Invalid');
			log_message('error',var_export($contentType,true));
			return false;
		}

		$response = $this->bot->uploadRichMenuImage($richMenuId, $imagePath, $contentType);
		
		return $this->responseHandler('uploadRichmenuImage',$response);

	}

	function downloadRichmenuImage($richMenuId)
	{
		$response = $this->bot->downloadRichMenuImage($richMenuId);

		return $response;
	}
	
	function deleteRichmenu($richMenuId) 
	{
		$response = $this->bot->deleteRichMenu($richMenuId);
		return $this->responseHandler('deleteRichmenu',$response);
	}

	####################################################################################################################################
	############################################                                            ############################################
	####################################################################################################################################

	private function responseHandler($origin_method = '', $response)
	{
		$httpStatusCode = $response->getHTTPstatus();

		/**
		 * 200 = Success
		 * 4xx = Do not retry
		 * 500 = LINE Bot error Use request id for retry later
		 */

		if ($response->isSucceeded()) {
			return true;
		}

		$requestId = $response->getHeader('X-Line-Request-Id');
		$body = $response->getJSONDecodedBody();

		$error_msg = "$origin_method (ReqId: $requestId) Failed ($httpStatusCode)";
		log_message('error', $error_msg);
		log_message('info', var_export($body, true));

		return false;
	}
}
