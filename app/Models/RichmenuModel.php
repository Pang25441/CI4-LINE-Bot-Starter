<?php namespace App\Models;

use CodeIgniter\Model;

class RichmenuModel extends Model
{
    protected $table      = 'richmenu';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields =  [
                                    'richmenuId',
                                    'name',
                                    'data',
                                    'created_datetime', 
                                    'updated_datetime',
                                    'deleted_datetime'
                                ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_datetime';
    protected $updatedField  = 'updated_datetime';
    protected $deletedField  = 'deleted_datetime';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}