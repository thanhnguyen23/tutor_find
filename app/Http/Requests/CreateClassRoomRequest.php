<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateClassRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Cho phép tất cả, hoặc bạn có thể custom check user role ở đây
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_id'            => 'required|exists:user_bookings,id',
            'topic'                 => 'required|string|max:255',
            'agenda'                => 'nullable|string',
            'scheduled_duration'    => 'required|integer|min:1',
            // Nếu frontend đã ghép date + time slot thành datetime
            'scheduled_start_time'  => 'nullable|date',
            'scheduled_end_time'    => 'nullable|date|after:scheduled_start_time',
            // Nếu gửi kèm
            'tutor_uid'             => 'nullable|string',
            'student_uid'           => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'booking_id.required'       => 'Thiếu booking_id',
            'booking_id.exists'         => 'Booking không tồn tại',
            'topic.required'            => 'Vui lòng nhập chủ đề',
            'scheduled_duration.required' => 'Vui lòng nhập thời lượng',
            'scheduled_duration.integer'  => 'Thời lượng phải là số nguyên',
            'scheduled_duration.min'      => 'Thời lượng phải lớn hơn 0',
            'scheduled_end_time.after'    => 'Thời gian kết thúc phải sau thời gian bắt đầu',
        ];
    }
}
