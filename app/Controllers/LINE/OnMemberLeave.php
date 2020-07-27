<?php

namespace App\Controllers\LINE;

use App\Controllers\LineController;

class OnMemberLeave extends LineController
{
    public function index()
    {
        log_message('debug','OnMemberLeave');

        // Delete Member from table
        $this->MemberUnregister();
    }

    private function MemberUnregister()
    {
        log_message('debug','MemberUnregister');

        $members = $this->event->left->members;

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

        foreach($members as $member)
        {
            if($member->type != 'user'){
                continue;
            }
            $modelMember->where('userId', $member->userId)->where($ref_id, $group_data['id'])->delete();
        }
        
        $modelMember->purgeDeleted();
        
    }
}