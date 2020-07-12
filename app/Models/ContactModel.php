<?php namespace App\Models;

use CodeIgniter\Model;

class ContactModel extends Model
{
    protected $table      = 'contacts';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
                                    'userId', 
                                    'displayName', 
                                    'language', 
                                    'profile_id', 
                                    'uniqueId', 
                                    'following', 
                                    'banned', 
                                    'banned_reason', 
                                    'follow_datetime', 
                                    'unfollow_datetime', 
                                ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_datetime';
    protected $updatedField  = 'updated_datetime';
    protected $deletedField  = 'deleted_datetime';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}