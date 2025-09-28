<?php

namespace App\Repositories\Eloquent;

use App\Models\ClassRoom;
use App\Repositories\Contracts\ClassRoomRepositoryInterface;
use App\Enums\ClassRoomAction;
use App\Jobs\ExpireClassRoomJob;
use Illuminate\Support\Carbon;

class ClassRoomRepository implements ClassRoomRepositoryInterface
{
    protected $model;

    public function __construct(ClassRoom $model)
    {
        $this->model = $model;
    }

    public function paginate(array $filters = [], int $perPage = 10, $uid)
    {
        // $this->updateExpireClassRoom($uid);

        $query1 = $this->model
        ->newQuery()
        ->where(function ($query) use ($uid) {
            $query
            ->whereOr('student_uid', $uid)
            ->whereOr('tutor_uid', $uid);
        })
        ->orderByDesc('created_at');

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query1->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query1->where(function ($q) use ($s) {
                $q->where('topic', 'like', "%{$s}%")
                  ->orWhere('agenda', 'like', "%{$s}%");
            });
        }

        return $query1->with('booking')->paginate($perPage);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function find(int $id)
    {
        return $this->model->with('booking')->findOrFail($id);
    }

    public function findZoom(int $zoom_id)
    {
        return $this->model->where('zoom_meeting_id', $zoom_id)->with('booking')->first();
    }

    public function update(int $id, array $data)
    {
        $item = $this->find($id);
        $item->update($data);
        return $item;
    }

    private function updateExpireClassRoom($uid) {
        $this->model
        ->where(function ($query) use ($uid) {
            $query
            ->whereOr('student_uid', $uid)
            ->whereOr('tutor_uid', $uid);
        })
        ->whereIn('status', [
            ClassRoomAction::Pending->value,
            ClassRoomAction::Scheduled->value,
        ])
        ->whereNotNull('scheduled_end_time')
        ->where('scheduled_end_time', '<', Carbon::now()->subMinutes(5))
        // ->where('scheduled_end_time', '>=', $timeLimit)
        ->each(function ($booking) {
            ExpireClassRoomJob::dispatch($booking);
        });
    }
}
