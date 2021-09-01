<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations, WithFaker;

    /** @test */
    public function an_user_can_register()
    {
        $user = [
            'name'                  => 'test',
            'email'                 => 'test@test.com',
            'password'              => 'Abc123',
            'password_confirmation' => 'Abc123'
        ];

        $this->json('post', '/api/auth/register', $user)
            ->assertOk()
            ->assertJsonStructure([
                'user',
                'access_token'
            ]);

        $this->assertDatabaseHas('users', [
            'name'      => 'test',
            'email'     => 'test@test.com',
        ]);
    }

    /** @test */
    public function an_user_can_login()
    {
        $user = User::factory()->create();

        $this->json('post', '/api/auth/login', ['email' => $user->email, 'password' => 'password'])
            ->assertOk()
            ->assertJsonStructure(['access_token']);
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->json('post', '/api/auth/logout')
            ->assertNoContent();
    }

    /** @test */
    public function an_user_can_self_data()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->json('get', '/api/auth/me')
            ->assertOk()
            ->assertJsonFragment(['name' => $user->name])
            ->assertJsonFragment(['email' => $user->email])
            ->assertJsonMissing(['password' => $user->password])
            ->assertJsonStructure([
                'user' => [
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }
}
