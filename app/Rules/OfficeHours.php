<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class OfficeHours implements Rule
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
        if ($datetime->isSaturday() || $datetime->isSunday()) {
            return false;
        }

        if ($datetime->hour < 9 || $datetime->hour > 18) {
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
        return trans('The appointment datetime  must be in office hours (9am to 6pm, Monday  to Friday).');
    }
}
