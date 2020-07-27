<?php namespace App\Models;

use CodeIgniter\Model;

class GroupMemberModel extends Model
{
    protected $table      = 'group_members';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = 
    [
        'uniqueId',
        'userId',
        'displayName',
        'group_id'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_datetime';
    protected $updatedField  = 'updated_datetime';
    protected $deletedField  = 'deleted_datetime';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}