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
                                    'display_name', 
                                    'language', 
                                    'profile_id', 
                                    'uniqueId', 
                                    'following', 
                                    'banned', 
                                    'banned_reason', 
                                    'created_datetime', 
                                    'follow_datetime', 
                                    'unfollow_datetime', 
                                    'updated_datetime',
                                    'deleted_datetime'
                                ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_datetime';
    protected $updatedField  = 'updated_datetime';
    protected $deletedField  = 'deleted_datetime';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}