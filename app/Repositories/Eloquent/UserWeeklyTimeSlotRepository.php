<?php

namespace App\Repositories\Eloquent;

use App\Models\UserWeeklyTimeSlot;
use App\Repositories\Contracts\UserWeeklyTimeSlotRepositoryInterface;
use Illuminate\Support\Collection;

class UserWeeklyTimeSlotRepository implements UserWeeklyTimeSlotRepositoryInterface
{
    protected $model;

    public function __construct(UserWeeklyTimeSlot $model)
    {
        $this->model = $model;
    }

    /**
     * Get all time slots for a specific user
     */
    public function getTimeSlotsByUser(int $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->orderBy('day_of_week_id')
            ->orderBy('time_slot_id')
            ->with(['timeSlot'])
            ->get();
    }

    /**
     * Create a new time slot
     */
    public function create(array $data): UserWeeklyTimeSlot
    {
        return $this->model->create($data);
    }

    /**
     * Bulk create time slots
     */
    public function createMany(array $items): Collection
    {
        $created = collect();
        foreach ($items as $item) {
            $created->push($this->model->create($item));
        }
        return $created;
    }

    /**
     * Update an existing time slot
     */
    public function update($id, $data)
    {

        $dataFind = $this->model->findOrFail($id);
        $dataFind->update($data);
        return $dataFind;
    }

    /**
     * Delete a time slot
     */
    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }

    /**
     * Get time slots for a specific user on a specific day
     */
    public function getByDay(int $userId, int $dayOfWeek): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('day_of_week_id', $dayOfWeek)
            ->orderBy('time_slot_id')
            ->get();
    }

    /**
     * Get time slots for a user within a specific time range
     */
    public function getByTimeRange(int $userId, string $startTime, string $endTime): Collection
    {
        // Not applicable with discrete time_slot_id model; return empty collection
        return collect();
    }

    /**
     * Get a time slot by ID
     */
    public function getTimeSlotById(int $id): ?UserWeeklyTimeSlot
    {
        return $this->model->find($id);
    }
}
