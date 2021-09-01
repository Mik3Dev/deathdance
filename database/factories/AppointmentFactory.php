<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Appointment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $time = $this->faker->time();
        return [
            'user_id'               => User::factory()->create(),
            'description'           => $this->faker->sentence(),
            'appointment_date'      => Carbon::now(),
            'start_time'            => Carbon::now()->toTimeString(),
            'end_time'              => Carbon::now()->addHour()->toTimeString(),
        ];
    }
}
