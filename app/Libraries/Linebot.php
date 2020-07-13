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

	####################################################################################################################################
	############################################                   MESSAGES                 ############################################
	####################################################################################################################################
	/**
	 * 
	 */

	public function pushTextProfile($profileIds, $text, $notificationDisabled=false)
	{
		if(!$profileIds || !$text){return false;}

		$contactModel = new \App\Models\ContactModel();
		$contacts = $contactModel->where(['profile_id'=>$profileIds, 'banned'=>0])->find();

		$userIds = [];

		foreach($contacts AS $c)
		{
			$userIds[] = $c['userId'];
		}

		if(!$userIds)
		{
			return false;
		}

		$messageObject = [];

		if(is_array($text))
		{
			foreach($text AS $t)
			{
				$messageObject[] = [
					'type' => 'text',
					'text' => $t
				];
			}
		}
		else
		{
			$messageObject[] = [
				'type' => 'text',
				'text' => $text
			];
		}

		return $this->pushMessage($userIds, $messageObject, $notificationDisabled);
	}

	public function pushMessageProfile($profileIds, array $messageObject, $notificationDisabled=false)
	{
		if(!$profileIds || !$messageObject){return false;}

		$contactModel = new \App\Models\ContactModel();
		$contacts = $contactModel->where(['profile_id'=>$profileIds, 'banned'=>0])->find();

		$userIds = [];

		foreach($contacts AS $c)
		{
			$userIds[] = $c['userId'];
		}

		if(!$userIds)
		{
			return false;
		}

		return $this->pushMessage($userIds, $messageObject, $notificationDisabled);
	}

	public function pushMessage($userIds, array $messageObject, $notificationDisabled=false)
	{
		# POST https://api.line.me/v2/bot/message/push
		# POST https://api.line.me/v2/bot/message/multicast

		if(!$userIds || !$messageObject){return false;}

		if(is_array($userIds))
		{
			$endpoint = 'https://api.line.me/v2/bot/message/multicast';
		}
		else
		{
			$endpoint = 'https://api.line.me/v2/bot/message/push';
		}

		$payload = [
			'to' => $userIds,
			'messages' => $messageObject
		];

		if($notificationDisabled)
		{
			$payload['notificationDisabled'] = $notificationDisabled;
		}

		$response = $this->httpClient('POST', $endpoint, json_encode($payload), 'Linebot.pushMessage');
		return $response;
	}

	public function replyMessage(array $messageObject, $replyToken = null, $notificationDisabled=false)
	{
		# POST https://api.line.me/v2/bot/message/reply

		if(!$messageObject){return false;}

		$replyToken = $replyToken ? $replyToken : $this->replyToken;

		if($replyToken)
		{
			return false;
		}

		$endpoint = 'https://api.line.me/v2/bot/message/reply';

		$payload = [
			'replyToken' => $replyToken,
			'messages' => $messageObject
		];

		if($notificationDisabled)
		{
			$payload['notificationDisabled'] = $notificationDisabled;
		}

		$response = $this->httpClient('POST', $endpoint, json_encode($payload), 'Linebot.replyMessage');
		return $response;
	}

	public function sendText($userIds = null, $text = null, $notificationDisabled = false)
	{
		if (!$text || !$userIds) 
		{
			return false;
		}

		if(is_array($userIds))
		{
			return $this->broadcastText($userIds, $text);
		}

		$textMessageBuilder = $this->buildTextMessage($text);

		$response = $textMessageBuilder ? $this->bot->pushMessage($userIds, $textMessageBuilder, $notificationDisabled) : false;

		return $this->responseHandler('sendTextMessage', $response);
	}

	public function broadcastText(array $userIds, $text = null, $notificationDisabled = false)
	{
		if(!$userIds || !$text){return false;}

		$messageObject = [];

		if(is_array($text))
		{
			foreach($text AS $t)
			{
				$messageObject[] = [
					'type' => 'text',
					'text' => $t
				];
			}
		}
		else
		{
			$messageObject[] = [
				'type' => 'text',
				'text' => $text
			];
		}

		return $this->pushMessage($userIds, $messageObject, $notificationDisabled);
	}

	public function replyText($text = null, $replyToken = null, $notificationDisabled = false)
	{
		$replyToken = $replyToken ? $replyToken : $this->replyToken;

		if (!$text || !$replyToken) 
		{
			return false;
		}

		$textMessageBuilder = $this->buildTextMessage($text);

		$response = $textMessageBuilder ? $this->bot->replyMessage($replyToken, $textMessageBuilder, $notificationDisabled) : false;

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

	####################################################################################################################################
	############################################                  RICH MENU                 ############################################
	####################################################################################################################################

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
		$this->responseHandler('downloadRichmenuImage',$response);
		if($response->getHTTPStatus()==200) 
		{
			return $response->getRawBody();
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

	function unlinkRichMenu($userId)
	{
		$response = $this->bot->unlinkRichMenu($userId);
		return $this->responseHandler('unlinkRichMenu',$response);
	}

	function unlinkRichMenuBulk($userIds=[])
	{
		# POST https://api.line.me/v2/bot/richmenu/bulk/unlink

		if(!is_array($userIds))
		{
			if(is_string($userIds))
			{
				return $this->unlinkRichMenu($userIds);
			}
			return false;
		}

		$response = $this->httpClient('POST', "https://api.line.me/v2/bot/richmenu/bulk/unlink", json_encode(['userIds' => $userIds]), 'unlinkRichMenuBulk');
		return $response ? true : false;
	}

	function unlinkRichMenuProfile($profileIds=[])
	{
		$contactModel = new \App\Models\ContactModel();
		$contacts = $contactModel->where('profile_id', $profileIds)->findAll();

		if(!$contacts){
			return false;
		} 

		$userIds=[];
		$contactIds=[];
		foreach($contacts AS $c)
		{
			$userIds[] = $c['userId'];
			$contactIds[] = $c['id'];
		}

		$response = $this->unlinkRichMenuBulk($userIds);

		if($response)
		{
			$contactModel->where($contactIds)->set('richmenu_id', null)->update();
		}
		
		return $response ? true : false;
	}

	function linkRichMenuProfile($richMenuName='', $profileIds=[])
	{
		# https://api.line.me/v2/bot/richmenu/bulk/link

		$contactModel = new \App\Models\ContactModel();
		if(is_array($profileIds))
		{

		}
		else
		{
			$profileIds = [$profileIds];
		}
		$contacts = $contactModel->where('profile_id', $profileIds)->findAll();

		if(!$contacts){
			return false;
		} 

		$userIds=[];
		$contactIds=[];
		foreach($contacts AS $c)
		{
			$userIds[] = $c['userId'];
			$contactIds[] = $c['id'];
		}

		$richmenuModel = new \App\Models\RichmenuModel();
		$richmenu = $richmenuModel->where('name',$richMenuName)->first();
		if(!$richmenu)
		{
			return false;
		}

		$response = $this->linkRichMenu($richmenu['richMenuId'], $userIds);

		if($response)
		{
			$contactModel->where($contactIds)->set('richmenu_id',$richmenu['id'])->update();
		}
		
		return $response ? true : false;
	}


	####################################################################################################################################
	############################################                   UTILIZE                  ############################################
	####################################################################################################################################

	private function responseHandler($origin_method = '', $response)
	{
		$httpStatusCode = $response->getHTTPstatus();

		/**
		 * 200 = Success
		 * 4xx = Do not retry
		 * 500 = LINE Bot error Use request id for retry later
		 */

		$body = $response->getJSONDecodedBody();

		if ($response->isSucceeded()) 
		{
			return $body;
		}

		$requestId = $response->getHeader('X-Line-Request-Id');
		
		$error_msg = "$origin_method (ReqId: $requestId) Failed ($httpStatusCode)";
		log_message('error', $error_msg);
		log_message('info', var_export($body, true));

		return false;
	}

	private function httpClient($method='POST', $endpoint, $JSONBody, $origin_method = 'httpClient') 
	{
		$curl = curl_init();

		$headers = [
			"authorization: Bearer " . $this->line->channelAccessToken,
			"cache-control: no-cache",
			"content-type: application/json"
		];
		
		curl_setopt_array($curl, [
			CURLOPT_URL 			=> $endpoint,
			CURLOPT_RETURNTRANSFER 	=> true,
			CURLOPT_MAXREDIRS 		=> 10,
			CURLOPT_TIMEOUT 		=> 30,
			CURLOPT_HTTP_VERSION 	=> CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST 	=> $method,
			CURLOPT_POSTFIELDS 		=> $JSONBody,
			CURLOPT_HTTPHEADER 		=> $headers,
		]);

		$response 	= curl_exec($curl);
		$err 		= curl_error($curl);
		$status 	= curl_getinfo($curl);
		
		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $result['header'] = substr($response, 0, $header_size);
        $result['body'] = substr( $response, $header_size );
        $result['http_code'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $result['last_url'] = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);

		curl_close($curl);

		if ($status['http_code'] !== 200) 
		{
			$error_msg = "$origin_method Failed ($status[http_code])";
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
