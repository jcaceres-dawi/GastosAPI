<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'jhondoe@gmail.com',
            'password' => 'password',
            'confirm_password' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'jhondoe@gmail.com',
        ]);
    }

    public function test_user_cannot_register_with_existing_email()
    {
        $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'jhondoe@gmail.com',
            'password' => 'password',
            'confirm_password' => 'password',
        ]);

        $failedResponse = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'jhondoe@gmail.com',
            'password' => 'password',
            'confirm_password' => 'password',
        ]);

        $failedResponse->assertStatus(409)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_user_cannot_register_without_name()
    {
        $response = $this->postJson('/api/register', [
            'email' => 'jhondoe@gmail.com',
            'password' => 'password',
            'confirm_password' => 'password',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_user_cannot_register_without_email()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'password' => 'password',
            'confirm_password' => 'password',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_user_cannot_register_without_password()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'jhondoe@gmail.com',

        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_can_get_user()
    {
        $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'jhondoe@gmail.com',
            'password' => 'password',
            'confirm_password' => 'password',
        ]);

        $user = User::where('email', 'jhondoe@gmail.com')->first();

        $response = $this->actingAs($user, 'api')->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJson([
                'name' => $user->name,
                'email' => $user->email,
            ]);
    }
}
