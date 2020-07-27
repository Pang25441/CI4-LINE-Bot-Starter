<?php

namespace App\Controllers;

use CodeIgniter\Controller;

use App\Libraries\Linebot;

class LineController extends Controller {
    
    protected $helpers = [];

	protected $event = null;
	protected $source = null;
	protected $sourceType = null;
	protected $replyToken = null;
	protected $userId = null;
	protected $groupId = null;
	protected $roomId = null;

	protected $trigger = '';
	
	protected $line;

	protected $linebot;
    
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);
		
        $this->receivePayload();
	}

	private function receivePayload() 
	{
		$this->line = new \Config\Line();

		$payload = file_get_contents('php://input');
		if($payload) 
		{
			log_message('debug', 'receivePayload ' . $payload);
			$decoded_payload = json_decode($payload);

			if(is_array($decoded_payload->events))
			{
				$this->event = $decoded_payload->events[0];

				$this->source = $this->event->source;
				$this->sourceType = $this->source->type;

				$this->userId = isset($this->source->userId) ? $this->source->userId : null;

				if($this->sourceType == 'group')
				{
					$this->groupId = $this->source->groupId;
				}

				if($this->sourceType == 'room')
				{
					$this->roomId = $this->source->roomId;
				}

				$this->replyToken = $this->event->replyToken;

			}
		}

		$this->linebot = new Linebot($this->event);

		if($this->event)
		{
			switch($this->sourceType)
			{
				case 'user':
					$this->checkContact($this->source->userId);
				break;

				case 'group':
					$this->checkGroupContact($this->groupId, $this->userId);
				break;

				case 'room':
					$this->checkRoomContact($this->roomId, $this->userId);
				break;
			}
			
		}
	}

	protected function checkContact(string $userId=null) 
	{
		log_message('debug','checkContact');
		if(!$userId){
			return false;
		}

		$contactModel = new \App\Models\ContactModel();

		$contact = $contactModel->where('userId', $userId)->first();
		
		if(!$contact) 
		{
			// Don't have contact yet

			$profile = $this->linebot->getProfile($userId);
			$data = [
				'userId' => $userId,
				'uniqueId' => uniqid(),
				'follow_datetime' => date('Y-m-d H:i:s')
			];

			if($profile)
			{
				$data['displayName'] = utf8_encode($profile['displayName']);
				$data['language'] = $profile['language'];
			}
			
			$contact_id = $contactModel->insert($data);
			$contact = $contactModel->find($contact_id);
			$contact_type = 'NEW_CONTACT';
		}
		else
		{
			// Update Profile Daily
			$contact_type = 'OLD_CONTACT';
		}

		// Set Language by user 
		$this->request->setLocale($contact['language']);
		return $contact_type;
	}

	protected function checkGroupContact(string $groupId = null, string $userId = null)
	{
		log_message('debug','checkGroupContact');
		// Group Register
		if(!$groupId)
		{
			return false;
		}
		$groupModel = new \App\Models\GroupModel();
		$group = $groupModel->where('groupId',$groupId)->first();
		if(!$group)
		{
			$summary = $this->linebot->getGroupSummary($groupId);
			$groupName = $summary ? $summary->groupName : $groupId;
			$data = [
				'groupId' => $groupId,
				'groupName' => $groupName,
				'status' => 1
			];

			$group_id = $groupModel->insert($data);
		}
		else
		{
			$group_id = $group['id'];
		}

		// contact Register
		if($userId)
		{
			$groupMemberModel = new \App\Models\GroupMemberModel();
			$member = $groupMemberModel->where('userId',$userId)->where('group_id',$group_id)->first();
			
			if(!$member)
			{
				$profile = $this->linebot->getProfileGroup($groupId, $userId);
				$data = [
					'userId' => $userId,
					'uniqueId' => uniqid(),
					'group_id' => $group_id
				];
	
				if($profile)
				{
					$data['displayName'] = utf8_encode($profile['displayName']);
				}
				log_message('debug','Group Member ' . var_export($data, true));
				$groupMemberModel->insert($data);
			}
		}
	}

	protected function checkRoomContact(string $roomId = null, string $userId = null)
	{
		log_message('debug','checkRoomContact');
		// Room Register
		if(!$roomId)
		{
			return false;
		}
		$roomModel = new \App\Models\RoomModel();
		$room = $roomModel->where('roomId',$roomId)->first();
		if(!$room)
		{
			$data = [
				'roomId' => $roomId,
				'status' => 1
			];

			$room_id = $roomModel->insert($data);
		}
		else
		{
			$room_id = $room['id'];
		}

		// contact Register
		if($userId)
		{
			$roomMemberModel = new \App\Models\RoomMemberModel();
			$member = $roomMemberModel->where('userId',$userId)->where('room_id',$room_id)->first();
			
			if(!$member)
			{
				$profile = $this->linebot->getProfileRoom($roomId, $userId);
				$data = [
					'userId' => $userId,
					'uniqueId' => uniqid(),
					'room_id' => $room_id
				];
	
				if($profile)
				{
					$data['displayName'] = utf8_encode($profile['displayName']);
				}
				log_message('debug','Room Member ' . var_export($data, true));
				$roomMemberModel->insert($data);
			}
		}
	}
	
}