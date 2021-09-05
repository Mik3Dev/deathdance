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
            'user_id'               => 1,
            'description'           => $description,
            'appointment_datetime'  => '01/01/2021 00:00:00',
            'end_datetime'          => '01/01/2021 01:00:00',
        ]);

        $this->assertDatabaseHas('appointments', [
            'user_id'           => 1,
            'description'       => $description,
            'appointment_datetime'  => '01/01/2021 00:00:00',
            'end_datetime'          => '01/01/2021 01:00:00',
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
                        'appointment_datetime',
                        'end_datetime',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    /** @test */
    public function an_user_can_create_an_appointment()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $appointment_date = Carbon::create(2021, 9, 1, 12, 0, 0, 'America/Santiago');
        $this->travelTo(Carbon::create(2021, 9, 1, 12, 0, 0)->subHours(10));

        $appointment = Appointment::factory()->raw([
            'description'           => 'lorem ipsum',
            'appointment_datetime'  => $appointment_date->toDateTimeLocalString(),
        ]);

        $this->json('post', '/api/appointments', $appointment)
            ->assertCreated()
            ->assertJsonFragment(['appointment_datetime' => $appointment_date]);

        $this->assertDatabaseHas('appointments', [
            'user_id'               => $user->id,
            'description'           => 'lorem ipsum',
            'appointment_datetime'  => $appointment_date->toISOString(),
            'end_datetime'          => $appointment_date->addHour()->toISOString(),
        ]);
    }

    /** @test */
    public function an_user_cannot_create_an_apointment_on_taken_hour()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Appointment::factory()->create([
            'appointment_datetime'  => Carbon::create(2021, 9, 1, 12, 0, 0),
            'end_datetime'  => Carbon::create(2021, 9, 1, 13, 0, 0),
        ]);

        $this->travelTo(Carbon::create(2021, 9, 1, 11, 0, 0));
        $appointment = Appointment::factory()->raw([
            'description'           => 'lorem ipsum',
            'appointment_datetime'  => Carbon::create(2021, 9, 1, 12, 00, 0)->toDateTimeLocalString(),
        ]);

        $this->json('post', '/api/appointments', $appointment)
            ->assertStatus(422);

        $appointment = Appointment::factory()->raw([
            'description'           => 'lorem ipsum',
            'appointment_datetime'  => Carbon::create(2021, 9, 1, 12, 59, 0)->toDateTimeLocalString(),
        ]);

        $this->json('post', '/api/appointments', $appointment)
            ->assertStatus(422);
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
                    'appointment_datetime',
                    'end_datetime',
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

        $appointment_date = Carbon::create(2021, 9, 1, 12, 0, 0, 'America/Santiago');
        $this->travelTo(Carbon::create(2021, 9, 1, 12, 0, 0)->subHours(2));

        $appointment = Appointment::factory()->create([
            'user_id'               => $user->id,
            'appointment_datetime'  => $appointment_date->toDateTimeLocalString()
        ]);

        $this->json('put', '/api/appointments/' . $appointment->id, [
            'description'           => 'new description',
            'appointment_datetime'  => $appointment_date->addDay()
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
                    'appointment_datetime',
                    'end_datetime',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertDatabaseHas('appointments', [
            'id'                    => $appointment->id,
            'user_id'               => $user->id,
            'description'           => 'new description',
            'appointment_datetime'  => $appointment_date->toISOString(),
        ]);
    }

    /** @test */
    public function an_user_cannont_update_an_appointment_that_does_not_belongs()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $appointment_date = Carbon::create(2021, 9, 1, 12, 0, 0, 'America/Santiago');
        $this->travelTo(Carbon::create(2021, 9, 1, 12, 0, 0)->subHours(2));

        $appointment = Appointment::factory()->create([
            'appointment_datetime'  => $appointment_date->toDateTimeLocalString()
        ]);

        $this->json('put', '/api/appointments/' . $appointment->id, [
            'description'           => 'new description',
            'appointment_datetime'  => $appointment_date->addDay()
        ])
            ->assertForbidden();

        $this->assertDatabaseMissing('appointments', [
            'id'                    => $appointment->id,
            'user_id'               => $user->id,
            'description'           => 'new description',
            'appointment_datetime'  => $appointment_date->toISOString(),
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

    /** @test */
    public function an_user_cannot_delete_an_appointment_that_does_not_belongs()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $appointment = Appointment::factory()->create();
        $this->json('delete', '/api/appointments/' . $appointment->id)
            ->assertForbidden();

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id
        ]);
    }

    /** @test */
    public function should_response_with_422_if_the_appointment_date_is_on_saturday_or_sunday()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Saturday
        $appointment_date = Carbon::create(2021, 1, 2, 12, 0, 0);
        $this->travelTo(Carbon::create(2021, 1, 1, 12, 0, 0));

        $appointment = Appointment::factory()->raw([
            'user_id'               => $user->id,
            'description'           => 'lorem ipsum',
            'appointment_datetime'  => $appointment_date->toISOString(),
        ]);

        $this->json('post', '/api/appointments', $appointment)
            ->assertStatus(422);

        // Sunday
        $appointment_date = Carbon::create(2021, 1, 3, 12, 0, 0);
        $appointment = Appointment::factory()->raw([
            'user_id'               => $user->id,
            'description'           => 'lorem ipsum',
            'appointment_datetime'  => $appointment_date->toISOString(),
        ]);
        $this->json('post', '/api/appointments', $appointment)
            ->assertStatus(422);

        $this->assertDatabaseMissing('appointments', [
            'user_id'               => $user->id,
            'description'           => 'lorem ipsum',
            'appointment_datetime'  => $appointment_date->toISOString(),
            'end_datetime'          => $appointment_date->addHour()->toISOString(),
        ]);
    }

    /** @test */
    public function it_should_response_with_422_if_the_time_is_out_of_office_hours()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $appointment_date = Carbon::create(2021, 9, 1, 8, 59, 59, 'America/Santiago');
        $this->travelTo(Carbon::create(2021, 9, 1, 0, 0, 0));

        $appointment = Appointment::factory()->raw([
            'user_id'               => $user->id,
            'description'           => 'lorem ipsum',
            'appointment_datetime'  => $appointment_date->toDateTimeLocalString(),
        ]);

        $this->json('post', '/api/appointments', $appointment)
            ->assertStatus(422);

        $appointment_date = Carbon::create(2021, 9, 1, 18, 00, 00, 'America/Santiago');
        $appointment = Appointment::factory()->raw([
            'user_id'               => $user->id,
            'description'           => 'lorem ipsum',
            'appointment_datetime'  => $appointment_date->toDateTimeLocalString(),
        ]);
    }
}
