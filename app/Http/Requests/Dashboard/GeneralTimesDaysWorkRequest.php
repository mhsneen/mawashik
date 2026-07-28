<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GeneralTimesDaysWorkRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

        public function rules()
    {
        return [
            'open_work' => 'required|in:0,1',
            'days_work' => ['nullable', 'array'],
            'days_work.*' => [
                'required',
                'string',
                Rule::in([
                    'saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday',
                ]),
            ],

            // التعديل هنا: إجبار الصيغة على 24 ساعة (ساعات:دقائق)
            'start_work' => ['nullable', 'array'],
            'start_work.*' => ['nullable', 'string', 'date_format:H:i'],

            'end_work' => ['nullable', 'array'],
            'end_work.*' => ['nullable', 'string', 'date_format:H:i'],

            'out_of_working_hours_message_ar' => 'nullable|max:800',
            'out_of_working_hours_message_en' => 'nullable|max:800',
        ];
    }

    public function attributes()
    {
        return [
            'open_work' => __('dashboard.open_work'),
            'start_work' => __('dashboard.start_work'),
            'end_work' => __('dashboard.end_work'),
            'days_work' => __('dashboard.days_work'),
            'out_of_working_hours_message_ar' => __('dashboard.Closing message'),
            'out_of_working_hours_message_en' => __('dashboard.Closing message'),
        ];
    }
}
