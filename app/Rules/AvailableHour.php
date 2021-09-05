<?php

namespace App\Rules;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class AvailableHour implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $datetime = Carbon::createFromDate($value);
        $appointment = Appointment::whereDate('appointment_datetime', $datetime->toDateString())
            ->whereTime('appointment_datetime', '<=', $datetime->toTimeString())
            ->whereTime('end_datetime', '>', $datetime->toTimeString())
            ->first();
        if (isset($appointment)) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('The choosen hour was taken.');
    }
}
