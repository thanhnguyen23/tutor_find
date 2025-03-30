<?php

namespace App\Repositories\Eloquent;

use App\Models\Subject;
use App\Repositories\Contracts\SubjectRepositoryInterface;

class SubjectRepository implements SubjectRepositoryInterface
{
    protected $subject;

    public function __construct(Subject $subject)
    {
        $this->subject = $subject;
    }
    // Triển khai các hàm của Interface
    public function getAll()
    {
        return $this->subject->all();
    }

    public function find($id)
    {
        return $this->subject->find($id);
    }
}