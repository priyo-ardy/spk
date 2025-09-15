<?php

namespace App\Models\Transaction\SPK\SPK;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Model;

class SPKDetailsModel extends Model
{
    protected $table            = 't_spk_details';
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

    function getLastRow(string $id_spk)
    {
        $query =  $this->select('*')
            ->where('id_spk', $id_spk)
            ->orderBy('urut', 'desc')
            ->limit(1)
            ->first();

        if ($query) {
            $baris = (int) $query->urut + 1;
        } else {
            $baris = 1;
        }

        return $baris;
    }
}
