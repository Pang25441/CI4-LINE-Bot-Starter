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
		if ($event) 
		{
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

		if ($response->isSucceeded()) 
		{
			return $profile;
		} 
		else 
		{
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
		if (!$text || !$userId) 
		{
			return false;
		}

		$textMessageBuilder = $this->buildTextMessage($text);

		$response = $textMessageBuilder ? $this->bot->pushMessage($userId, $textMessageBuilder) : false;

		return $this->responseHandler('sendTextMessage', $response);
	}

	public function replyText($replyToken = null, $text = null)
	{
		if (!$text || !$replyToken) 
		{
			return false;
		}

		$textMessageBuilder = $this->buildTextMessage($text);

		$response = $textMessageBuilder ? $this->bot->replyMessage($replyToken, $textMessageBuilder) : false;

		return $this->responseHandler('replyTextMessage', $response);
	}

	private function buildTextMessage($text)
	{
		if (is_array($text)) 
		{
			switch (count($text)) 
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


	public function getRichmenuList()
	{
		$response = $this->bot->getRichMenuList();

		if ($this->responseHandler('getRichmenuList', $response)) 
		{
			return $response->getJSONDecodedBody();
		} 
		else 
		{
			return false;
		}
	}

	public function getRichmenuImage($richMenuId)
	{
		$response = $this->bot->downloadRichMenuImage($richMenuId);

		if ($this->responseHandler('getRichmenuList', $response)) 
		{
			return $response->getJSONDecodedBody();
		} 
		else 
		{
			return false;
		}
	}

	public function createRichmenu($richMenuJSON)
	{
		$response = $this->httpClient('POST', 'https://api.line.me/v2/bot/richmenu', $richMenuJSON, 'createRichmenu');

		return $response ? $response->richMenuId : false;
	}

	function uploadRichmenuImage($richMenuId, $imagePath) 
	{
		$contentType = mime_content_type($imagePath);
		if(!in_array($contentType, ['image/jpeg', 'image/png']))
		{
			log_message('error','Rich Menu Image Type Invalid');
			log_message('error',var_export($contentType,true));
			$this->deleteRichmenu($richMenuId);
			return false;
		}

		$response = $this->bot->uploadRichMenuImage($richMenuId, $imagePath, $contentType);
		
		return $this->responseHandler('uploadRichmenuImage',$response);
	}

	function downloadRichmenuImage($richMenuId)
	{
		$response = $this->bot->downloadRichMenuImage($richMenuId);
		if($this->responseHandler('downloadRichmenuImage',$response)) 
		{
			return $response;
		}
		else
		{
			return false;
		}
	}
	
	function deleteRichmenu($richMenuId) 
	{
		$response = $this->bot->deleteRichMenu($richMenuId);
		return $this->responseHandler('deleteRichmenu',$response);
	}

	function setDefaultRichMenu($richmenu_name='')
	{
		if( $richMenuId = $this->getRichMenuId($richmenu_name) )
		{
			# https://api.line.me/v2/bot/user/all/richmenu/{richMenuId}
			$response = $this->httpClient('POST', "https://api.line.me/v2/bot/user/all/richmenu/$richMenuId", '', 'setDefaultRichMenu');
			log_message('info',var_export($response,true));
			return $response ? true : false;
		}

		return false;
	}

	function getDefaultRichMenuId()
	{
		$response = $this->httpClient('GET', "https://api.line.me/v2/bot/user/all/richmenu", '', 'getDefaultRichMenuId');
		log_message('info',var_export($response,true));
		return $response ? $response->richMenuId : false;
	}

	function cancelDefaultRichMenu() 
	{
		$response = $this->httpClient('DELETE', "https://api.line.me/v2/bot/user/all/richmenu", '', 'cancelDefaultRichMenu');
		return $response ? true : false;
	}

	function linkRichMenu($richMenuId, $userIds)
	{
		# https://api.line.me/v2/bot/richmenu/bulk/link
		$response = $this->httpClient('POST', "https://api.line.me/v2/bot/richmenu/bulk/link", json_encode(['richMenuId' => $richMenuId, 'userIds' => $userIds]), 'setDefaultRichMenu');
		return $response ? true : false;
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

		if ($response->isSucceeded()) 
		{
			return true;
		}

		$requestId = $response->getHeader('X-Line-Request-Id');
		$body = $response->getJSONDecodedBody();

		$error_msg = "$origin_method (ReqId: $requestId) Failed ($httpStatusCode)";
		log_message('error', $error_msg);
		log_message('info', var_export($body, true));

		return false;
	}

	private function httpClient($method='POST',$endpoint,$JSONBody,$origin_method = 'httpClient') 
	{
		$curl = curl_init();
		
		curl_setopt_array($curl, [
			CURLOPT_URL 			=> $endpoint,
			CURLOPT_RETURNTRANSFER 	=> true,
			CURLOPT_MAXREDIRS 		=> 10,
			CURLOPT_TIMEOUT 		=> 30,
			CURLOPT_HTTP_VERSION 	=> CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST 	=> $method,
			CURLOPT_POSTFIELDS 		=> $JSONBody,
			CURLOPT_HTTPHEADER 		=> [
										"authorization: Bearer " . $this->line->channelAccessToken,
										"cache-control: no-cache",
										"content-type: application/json"
										],
		]);

		$response 	= curl_exec($curl);
		$err 		= curl_error($curl);
		$status 	= curl_getinfo($curl);
		$http_code 	= $status['http_code'];

		curl_close($curl);

		if ($http_code !== 200) 
		{
			$error_msg = "$origin_method Failed ($http_code)";
			log_message('error', $error_msg);
			log_message('info', $response);
			return false;
		} 
		else 
		{
			return json_decode($response);
		}
	}

	private function getRichMenuId($richmenu_name)
	{
		$richmenuModel = new \App\Models\RichmenuModel();
		$richmenu = $richmenuModel->where('name',$richmenu_name)->first();

		if($richmenu)
		{
			return $richmenu['richMenuId'];
		}
		else
		{
			return false;
		}
	}
}
