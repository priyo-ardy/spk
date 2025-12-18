<?php

namespace App\Repositories\SPK;

interface SPKRepositoryInterface
{
    // Basic CRUD
    public function find(string $id): ?object;
    public function findWithDetails(string $id): array;
    public function create(array $data): string;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;

    // Navigation
    public function getPrevData(string $code): ?object;
    public function getNextData(string $code): ?object;

    // Specific Logic
    public function updateStatus(string $id, string $status, string $updatedBy): bool;
    public function generateDocNo(string $prefix, string $date, string $moldNo): string;

    // Detail/Image Management
    public function createDetail(array $data): bool;
    public function deleteDetail(string $id): bool;
    public function getDetailById(string $id): ?object;
    public function getLastDetailRow(string $spkId): int;
    public function getDetailsBySpkId(string $spkId): array;

    // Transaction Management
    public function beginTransaction(): void;
    public function commitTransaction(): bool;
    public function rollbackTransaction(): void;
}
