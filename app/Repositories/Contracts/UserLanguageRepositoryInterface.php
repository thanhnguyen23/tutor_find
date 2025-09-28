<?php

namespace App\Repositories\Contracts;

interface UserLanguageRepositoryInterface
{
    public function getLanguagesByUser(int $uid);
    public function create(array $data);
    public function update(array $data, $id);
    public function delete(int $id);
    public function find($id);
    public function all();
    /**
     * Kiểm tra user đã có ngôn ngữ này chưa
     */
    public function existsByUserAndLanguage(string $uid, int $languageId): bool;
    /**
     * Kiểm tra user đã có ngôn ngữ mẹ đẻ chưa
     */
    public function existsNativeLanguage(string $uid): bool;
}
