<?php

namespace App\Models\AppSetup\JobQueue;

use CodeIgniter\Model;

class QueueModel extends Model
{
    protected $table            = 'job_queue';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at';

    public function getPendingJobs()
    {
        return $this->where('status', 'pending')->orderBy('created_at', 'asc')->limit(25)->findAll();
    }

    public function updateJobStatus(string $id, string $status)
    {
        return $this->update($id, ['status' => $status]);
    }
}
