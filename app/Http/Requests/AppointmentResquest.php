<?php

namespace App\Http\Requests;

use App\Rules\AvailableHour;
use App\Rules\OfficeHours;
use Illuminate\Foundation\Http\FormRequest;

class AppointmentResquest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'description'           => 'nullable|max:255',
            'appointment_datetime'  => [
                'required',
                'date',
                'after_or_equal:today',
                new OfficeHours,
                new AvailableHour
            ],
        ];
    }
}
