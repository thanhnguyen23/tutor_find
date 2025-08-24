<?php

namespace App\Repositories\Contracts;

interface UserLanguageRepositoryInterface
{
    public function getLanguagesByUser(int $userId);
    public function create(array $data);
    public function update(array $data, $id);
    public function delete(int $id);
    public function find($id);
    public function all();
    /**
     * Kiểm tra user đã có ngôn ngữ này chưa
     */
    public function existsByUserAndLanguage(int $userId, int $languageId, int $excludeId = null): bool;
    /**
     * Kiểm tra user đã có ngôn ngữ mẹ đẻ chưa
     */
    public function existsNativeLanguage(int $userId, int $excludeId = null): bool;
}
