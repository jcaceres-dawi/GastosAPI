<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;
    protected $authToken;

    protected function setUp(): void
    {
        parent::setUp();

        $registerResponse = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'jhondoe@gmail.com',
            'password' => 'password',
            'confirm_password' => 'password',
        ]);

        $this->authToken = $registerResponse['data']['token'];
    }

    public function test_get_all_expenses()
    {
        $response = $this->getJson('/api/expenses', [
            'Authorization' => "Bearer {$this->authToken}",
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        "id",
                        "amount",
                        "description",
                        "category",
                        "user_id",
                        "created_at",
                        "updated_at"
                    ],
                ],
            ]);
    }

    public function test_create_expense()
    {
        $response = $this->postJson('/api/expenses', [
            'amount' => 100,
            'description' => 'Test expense',
            'category' => 'comida',
        ], [
            'Authorization' => "Bearer {$this->authToken}",
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                "message" => "Expense created successfully",
            ]);
    }

    public function test_create_expense_failed()
    {
        $response = $this->postJson('/api/expenses', [
            'amount' => 100,
            'description' => 'Test expense',
            'category' => 'comida',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                "message" => "Unauthenticated.",
            ]);
    }

    public function test_update_expense()
    {
        $responseExpenses = $this->postJson('/api/expenses', [
            'amount' => 100,
            'description' => 'Test expense',
            'category' => 'comida',
        ], [
            'Authorization' => "Bearer {$this->authToken}",
        ]);

        $expenseId = $responseExpenses['data']['id'];

        $response = $this->putJson("/api/expenses/{$expenseId}", [
            'amount' => 200,
            'description' => 'Test expense updated',
            'category' => 'ocio',
        ], [
            'Authorization' => "Bearer {$this->authToken}",
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                "message" => "Expense updated successfully",
            ]);
    }

    public function test_update_expense_failed()
    {
        $response = $this->putJson("/api/expenses/1", [
            'amount' => 200,
            'description' => 'Test expense updated',
            'category' => 'ocio',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                "message" => "Unauthenticated.",
            ]);
    }

    public function test_delete_expense()
    {
        $responseExpenses = $this->postJson('/api/expenses', [
            'amount' => 100,
            'description' => 'Test expense',
            'category' => 'comida',
        ], [
            'Authorization' => "Bearer {$this->authToken}",
        ]);

        $expenseId = $responseExpenses['data']['id'];

        $response = $this->deleteJson("/api/expenses/{$expenseId}", [
            'Authorization' => "Bearer {$this->authToken}",
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                "message" => "Expense deleted successfully",
            ]);
    }

    public function test_delete_expense_failed()
    {
        $response = $this->deleteJson("/api/expenses/1");

        $response->assertStatus(401)
            ->assertJson([
                "message" => "Unauthenticated.",
            ]);
    }

    public function test_list_category_expense()
    {

        $this->postJson('/api/expenses', [
            'amount' => 100,
            'description' => 'Test expense',
            'category' => 'comida',
        ], [
            'Authorization' => "Bearer {$this->authToken}",
        ]);


        $response = $this->getJson('/api/expenses/comida', [
            'Authorization' => "Bearer {$this->authToken}",
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        "id",
                        "amount",
                        "description",
                        "category",
                        "user_id",
                        "created_at",
                        "updated_at"
                    ],
                ],
            ]);
    }

    public function test_list_category_expense_failed()
    {
        $this->postJson('/api/expenses', [
            'amount' => 100,
            'description' => 'Test expense',
            'category' => 'comida',
        ], [
            'Authorization' => "Bearer {$this->authToken}",
        ]);

        $response = $this->getJson('/api/expenses/comidas', [
            'Authorization' => "Bearer {$this->authToken}",
        ]);

        $response->assertStatus(400)
            ->assertJson([
                "message" => "Invalid category",
                "success" => false,
            ]);
    }
}
