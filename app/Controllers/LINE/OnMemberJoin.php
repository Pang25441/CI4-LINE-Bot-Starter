<?php

namespace App\Controllers\LINE;

use App\Controllers\LineController;

class OnMemberJoin extends LineController
{
    public function index()
    {
        log_message('debug','OnMemberJoin');
        $this->MemberRegister();
    }

    private function MemberRegister()
    {
        log_message('debug','MemberRegister');

        $members = $this->event->joined->members;

        if($this->sourceType == 'group')
        {
            $model = new \App\Models\GroupModel();
            $modelMember = new \App\Models\GroupMemberModel();
            $key = 'groupId';
            $ref_id = 'group_id';
            $value = $this->groupId;
        }
        else if($this->sourceType == 'room')
        {
            $model = new \App\Models\RoomModel();
            $modelMember = new \App\Models\RoomMemberModel();
            $key = 'roomId';
            $ref_id = 'room_id';
            $value = $this->roomId;
        }

        $group_data = $model->where($key, $value)->first();

        if($members)
		{
            foreach($members as $member)
            {
                $member_data = $modelMember->where('userId',$member->userId)->where($ref_id, $group_data['id'])->first();

                if($this->sourceType == 'group')
                {
                    $profile = $this->linebot->getProfileGroup($value, $member->userId);
                }
                else if($this->sourceType == 'room')
                {
                    $profile = $this->linebot->getProfileRoom($value, $member->userId);
                }
                
                if(!$member_data)
                {
                    $data = [
                        'userId' => $member->userId,
                        'uniqueId' => uniqid(),
                        $ref_id => $group_data['id']
                    ];
        
                    if($profile)
                    {
                        $data['displayName'] = utf8_encode($profile['displayName']);
                    }
                    
                    $modelMember->insert($data);
                }
                else
                {
                    $member_data['displayName'] = utf8_encode($profile['displayName']);
                    $modelMember->save($member_data);
                }
            }
            
			
		}
    }
}