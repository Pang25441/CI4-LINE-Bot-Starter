<?php

namespace App\Controllers\LINE;

use App\Controllers\LineController;

class OnJoinGroup extends LineController
{
    public function index()
    {
        log_message('debug','OnJoinGroup');
        $this->GroupRegister();
    }

    private function GroupRegister()
    {
        log_message('debug','GroupRegister');
        // Use LineController::checkGroupContact
        // Use LineController::checkRoomContact

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

        $data = $model->where($key, $value)->first();
        $data['follow_datetime'] = date('Y-m-d H:i:s');
        $data['status'] = 1;

        $model->save($data);

    }

    private function syncGroupMembers()
    {
        /**
         * Premium Acount Feature
         */
        
    }
}