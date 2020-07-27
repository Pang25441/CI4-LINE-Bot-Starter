<?php namespace App\Models;

use CodeIgniter\Model;

class GroupModel extends Model
{
    protected $table      = 'groups';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = 
    [
        'groupId',
        'groupName',
        'status',
        'follow_datetime',
        'unfollow_datetime'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_datetime';
    protected $updatedField  = 'updated_datetime';
    protected $deletedField  = 'deleted_datetime';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}