<?php

namespace Tests\Feature;

use App\Models\Todo;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class TodoTest extends TestCase
{

    use DatabaseMigrations;

    public function test_todoOneCategory(): void
    {
        Todo::factory()->create();

        $response = $this->get('/api/todos');

        $response->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message', 'success', 'data'])
                    ->has('data', 1, function (AssertableJson $json) {
                        $json->hasAll(['id', 'name', 'completed', 'created_at', 'updated_at', 'category_id', 'category'])
                            ->has('category', function (AssertableJson $json) {
                                $json->hasAll(['id', 'name']);
                            });
                    });
            });
    }

    public function test_createTodo_success(): void
    {
        $testData = ['name' => 'Test task', 'completed' => false, 'category_id' => 1];

        $response = $this->postJson('/api/todos', $testData);
        $response->assertStatus(200)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'success']);
            });

        $this->assertDatabaseHas('todos', $testData);
    }

    public function test_createTodo_failureOutOfRange(): void
    {
        $testData = ['name' => 'Test task', 'completed' => false, 'category_id' => 8];

        $response = $this->postJson('/api/todos', $testData);
        $response->assertStatus(422)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });
    }

    public function test_createTodo_failureRequired(): void
    {
        $testData = ['name' => 'Test task', 'completed' => false];

        $response = $this->postJson('/api/todos', $testData);
        $response->assertStatus(422)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });
    }

    public function test_createTodo_malformed(): void
    {
        $testData = ['name' => 'Test task', 'completed' => 'no', 'category_id' => 2];

        $response = $this->postJson('/api/todos', $testData);
        $response->assertStatus(422)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });
    }

    public function test_updateTodo_success(): void
    {
        Todo::factory()->create();

        $testData = ['name' => 'Test task', 'completed' => false, 'category_id' => 1];

        $response = $this->putJson('/api/todos/1', $testData);
        $response->assertStatus(200)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'success']);
            });

        $this->assertDatabaseHas('todos', $testData);
    }

    public function test_updateTodo_failureOutOfRange(): void
    {
        Todo::factory()->create();

        $testData = ['name' => 'Test task', 'completed' => false, 'category_id' => 8];

        $response = $this->putJson('/api/todos/1', $testData);
        $response->assertStatus(422)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });
    }

    public function test_updateTodo_failureRequired(): void
    {
        Todo::factory()->create();

        $testData = ['name' => 'Test task', 'completed' => false];

        $response = $this->putJson('/api/todos/1', $testData);
        $response->assertStatus(422)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });
    }

    public function test_updateTodo_malformed(): void
    {
        Todo::factory()->create();

        $testData = ['name' => 'Test task', 'completed' => 'no', 'category_id' => 2];

        $response = $this->putJson('/api/todos/1', $testData);
        $response->assertStatus(422)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });
    }

    public function test_deleteTodo(): void
    {
        Todo::factory()->create();

        $response = $this->delete('api/todos/1');
        $response->assertStatus(200)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'success']);
            });

        $this->assertDatabaseEmpty('todos');
    }
}
