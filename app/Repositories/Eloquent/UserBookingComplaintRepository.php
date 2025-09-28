<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\UserBookingComplaintRepositoryInterface;
use App\Models\UserBookingComplaint;

class UserBookingComplaintRepository implements UserBookingComplaintRepositoryInterface
{
    protected $model;

    public function __construct(UserBookingComplaint $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function findById(int $id)
    {
        return $this->model->with(['booking', 'user', 'complaintType'])->find($id);
    }

    public function getByBookingId(int $bookingId)
    {
        return $this->model->with(['booking', 'user', 'complaintType'])
            ->where('booking_id', $bookingId)
            ->orderByDesc('id')
            ->get();
    }

    public function updateStatus(int $id, string $status, ?string $note = null)
    {
        $complaint = $this->model->findOrFail($id);
        $complaint->status = $status;
        if (!empty($note)) {
            // append note to description for quick auditing
            $complaint->description = trim(($complaint->description ? ($complaint->description . "\n") : '') . '[NOTE] ' . $note);
        }
        $complaint->save();
        return $complaint;
    }
}
