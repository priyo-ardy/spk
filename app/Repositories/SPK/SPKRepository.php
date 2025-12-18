<?php

namespace App\Repositories\SPK;

use App\Models\Transaction\SPK\SPK\SPKModel;
use App\Models\Transaction\SPK\SPK\SPKDetailsModel;
use Config\Database;

class SPKRepository implements SPKRepositoryInterface
{
    protected SPKModel $spkModel;
    protected SPKDetailsModel $detailModel;
    protected $db;

    public function __construct()
    {
        $this->spkModel = new SPKModel();
        $this->detailModel = new SPKDetailsModel();
        $this->db = Database::connect();
    }

    public function find(string $id): ?object
    {
        return $this->spkModel->where('id', $id)->first();
    }

    public function findWithDetails(string $id): array
    {
        $header = $this->find($id);
        $details = $this->detailModel
            ->where('id_spk', $id)
            ->orderBy('urut', 'asc')
            ->findAll();

        return [
            'header' => $header,
            'details' => $details
        ];
    }

    public function create(array $data): string
    {
        $id = generate_uuid();
        $data['id'] = $id;

        $this->spkModel->insert($data);

        return $id;
    }

    public function update(string $id, array $data): bool
    {
        return $this->spkModel->update($id, $data);
    }

    public function delete(string $id): bool
    {
        return $this->spkModel->delete($id);
    }

    public function getPrevData(string $code): ?object
    {
        return $this->spkModel->getPrevData($code);
    }

    public function getNextData(string $code): ?object
    {
        return $this->spkModel->getNextData($code);
    }

    public function updateStatus(string $id, string $status, string $updatedBy): bool
    {
        return $this->update($id, [
            'dokumen_status' => $status,
            'updated_by' => $updatedBy
        ]);
    }

    public function generateDocNo(string $prefix, string $date, string $moldNo): string
    {
        return $this->spkModel->generateDocNo($prefix, $date, $moldNo);
    }

    // Detail methods
    public function createDetail(array $data): bool
    {
        return (bool) $this->detailModel->insert($data);
    }

    public function deleteDetail(string $id): bool
    {
        return $this->detailModel->delete($id, true);
    }

    public function getDetailById(string $id): ?object
    {
        return $this->detailModel->where('id', $id)->first();
    }

    public function getLastDetailRow(string $spkId): int
    {
        return $this->detailModel->getLastRow($spkId);
    }

    public function getDetailsBySpkId(string $spkId): array
    {
        return $this->detailModel
            ->where('id_spk', $spkId)
            ->orderBy('urut', 'asc')
            ->findAll();
    }

    // Transaction helpers
    public function beginTransaction(): void
    {
        $this->db->transStart();
    }

    public function commitTransaction(): bool
    {
        $this->db->transComplete();
        return $this->db->transStatus();
    }

    public function rollbackTransaction(): void
    {
        $this->db->transRollback();
    }
}
