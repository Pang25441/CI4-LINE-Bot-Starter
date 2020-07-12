<?php namespace App\Models;

use CodeIgniter\Model;

class RichmenuModel extends Model
{
    protected $table      = 'richmenu';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields =  [
                                    'richMenuId',
                                    'name',
                                    'data',
                                    'isDefault'
                                ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_datetime';
    protected $updatedField  = 'updated_datetime';
    protected $deletedField  = 'deleted_datetime';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}