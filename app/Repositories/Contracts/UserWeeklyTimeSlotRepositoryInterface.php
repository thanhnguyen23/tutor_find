<?php

namespace App\Repositories\Contracts;

use App\Models\UserWeeklyTimeSlot;
use Illuminate\Support\Collection;

interface UserWeeklyTimeSlotRepositoryInterface
{
    /**
     * Get all time slots for a specific user
     */
    public function getTimeSlotsByUser(int $userId): Collection;

    /**
     * Create a new time slot
     */
    public function create(array $data): UserWeeklyTimeSlot;

    /**
     * Update an existing time slot
     */
    public function update($id, $data);

    /**
     * Delete a time slot
     */
    public function delete(int $id): bool;

    /**
     * Get time slots for a specific user on a specific day
     */
    public function getByDay(int $userId, int $dayOfWeek): Collection;

    /**
     * Get time slots for a user within a specific time range
     */
    public function getByTimeRange(int $userId, string $startTime, string $endTime): Collection;

    /**
     * Get a time slot by ID
     */
    public function getTimeSlotById(int $id): ?UserWeeklyTimeSlot;
}
