<?php

namespace App\Modules\__Module__\Models;

use CodeIgniter\Model;

class __ClassName__ extends Model
{
    protected $table = '__table__';
    protected $primaryKey = 'id';
    protected $allowedFields = [__fields__];
    
    // Comente regras não desejadas
    protected $validationRules = __validationRules__;
    
    // obriga ter o campo "created_at"
    // protected $useTimestamps = true;
    
    // obriga o campo deleted_at
    // protected $useSoftDeletes = true;    
}
