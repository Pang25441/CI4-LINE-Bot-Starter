<?php namespace App\Models;

use CodeIgniter\Model;

class RoomMemberModel extends Model
{
    protected $table      = 'room_members';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = 
    [
        'uniqueId',
        'userId',
        'displayName',
        'language',
        'room_id'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_datetime';
    protected $updatedField  = 'updated_datetime';
    protected $deletedField  = 'deleted_datetime';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}