<?php

namespace App\Controllers\LINE;

use App\Controllers\LineController;

class OnLeaveGroup extends LineController
{
    public function index()
    {
        log_message('debug','OnLeaveGroup');

        /* Inactive group keep member */
        $this->GroupInactive();

         
         /* Delete Group and Member from list */
        // $this->GroupUnregister();
    }

    private function GroupInactive()
    {
        log_message('debug','GroupInactive');

        if($this->sourceType == 'group')
        {
            $model = new \App\Models\GroupModel();
            $data = $model->where('groupId', $this->groupId)->first();
        }
        else if($this->sourceType == 'room')
        {
            $model = new \App\Models\RoomModel();
            $data = $model->where('roomId', $this->roomId)->first();
        }

        $data['status'] = 0;
        $data['unfollow_datetime'] = date('Y-m-d H:i:s');
        $model->save($data);
        
    }

    private function GroupUnregister() 
    {
        log_message('debug','GroupUnregister');

        if($this->sourceType == 'group')
        {
            $model = new \App\Models\GroupModel();
            $key = 'groupId';
            $value = $this->groupId;
        }
        else if($this->sourceType == 'room')
        {
            $model = new \App\Models\RoomModel();
            $key = 'roomId';
            $value = $this->roomId;
        }

        $model->where($key, $value)->delete();
        $model->purgeDeleted();
    }
}