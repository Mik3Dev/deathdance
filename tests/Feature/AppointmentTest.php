<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Sanctum\Sanctum;

class AppointmentTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations, WithFaker;

    /** @test */
    public function can_create_a_single_appointment()
    {
        $description = $this->faker()->sentence();
        Appointment::create([
            'user_id'           => 1,
            'description'       => $description,
            'appointment_date'  => '01/01/2021',
            'start_time'        => '00:00:00',
            'end_time'          => '01:00:00',
        ]);

        $this->assertDatabaseHas('appointments', [
            'user_id'           => 1,
            'description'       => $description,
            'appointment_date'  => '01/01/2021',
            'start_time'        => '00:00:00',
            'end_time'          => '01:00:00',
        ]);
    }

    /** @test */
    public function an_appointment_belongs_to_an_user()
    {
        $user = User::factory()->create();
        $appointments = Appointment::factory()->count(2)->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Collection::class, $user->appointments);
        $this->assertInstanceOf(Appointment::class, $user->appointments[0]);
        $this->assertCount(2, $user->appointments);
        $this->assertInstanceOf(User::class, $appointments[0]->user);
    }

    /** @test */
    public function anyone_can_access_the_index_of_appointments()
    {
        Appointment::factory()->count(10)->create();

        $this->json('get', '/api/appointments')
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'user' => [
                            'id',
                            'name',
                            'email'
                        ],
                        'description',
                        'appointment_date',
                        'start_time',
                        'end_time',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    /** @test */
    public function an_user_cab_create_an_appointment()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $appointment_date = Carbon::now();
        $start_time = Carbon::now()->toTimeString();
        $end_time = Carbon::now()->addHour()->toTimeString();

        $appointment = Appointment::factory()->raw([
            'user_id'               => $user->id,
            'description'           => 'lorem ipsum',
            'appointment_date'      => $appointment_date,
            'start_time'            => $start_time,
            'end_time'              => $end_time,
        ]);

        $this->json('post', '/api/appointments', $appointment)
            ->assertCreated();

        $this->assertDatabaseHas('appointments', [
            'user_id'               => $user->id,
            'description'           => 'lorem ipsum',
            'appointment_date'      => $appointment_date,
            'start_time'            => $start_time,
            'end_time'              => $end_time,
        ]);
    }

    /** @test */
    public function an_user_ca_view_a_single_appoinment()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $appointment = Appointment::factory()->create(['user_id' => $user->id]);
        $this->json('get', '/api/appointments/' . $appointment->id)
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user' => [
                        'id',
                        'name',
                        'email'
                    ],
                    'description',
                    'appointment_date',
                    'start_time',
                    'end_time',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /** @test */
    public function an_user_can_update_an_appointment()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $appointment = Appointment::factory()->create(['user_id' => $user->id]);
        $this->json('put', '/api/appointments/' . $appointment->id, [
            'description'           => 'new description',
            'appointment_date'      => '2025/01/01 00:00:00',
            'start_time'            => '09:00:00',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user' => [
                        'id',
                        'name',
                        'email'
                    ],
                    'description',
                    'appointment_date',
                    'start_time',
                    'end_time',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertDatabaseHas('appointments', [
            'id'                => $appointment->id,
            'user_id'           => $user->id,
            'description'       => 'new description',
            'appointment_date'  => '2025-01-01 00:00:00',
            'start_time'        => '09:00:00',
        ]);
    }

    /** @test */
    public function an_user_can_delete_an_appointment()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $appointment = Appointment::factory()->create(['user_id' => $user->id]);
        $this->json('delete', '/api/appointments/' . $appointment->id)
            ->assertOk();

        $this->assertDatabaseMissing('appointments', [
            'id' => $appointment->id
        ]);
    }
}
