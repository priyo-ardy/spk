<?php

namespace App\Models\MasterData\CommonData\ProblemCategory;

use CodeIgniter\Model;

class ProblemCategoryModel extends Model
{
    protected $table            = 'm_problem_category';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getDataById($id)
    {
        return $this->where('id', $id)
            ->select('id, category, code, name, remark')
            ->first();
    }
}
