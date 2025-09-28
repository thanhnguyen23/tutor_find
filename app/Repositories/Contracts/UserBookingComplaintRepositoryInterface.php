<?php

namespace App\Repositories\Contracts;

interface UserBookingComplaintRepositoryInterface
{
    public function create(array $data);

    public function findById(int $id);

    public function getByBookingId(int $bookingId);

    public function updateStatus(int $id, string $status, ?string $note = null);
}
