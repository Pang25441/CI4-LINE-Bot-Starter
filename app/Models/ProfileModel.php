<?php namespace App\Models;

use CodeIgniter\Model;

class ProfileModel extends Model
{
    protected $table      = 'profiles';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
                                    'firstname', 
                                    'lastname', 
                                    'email', 
                                    'password', 
                                    'created_datetime', 
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