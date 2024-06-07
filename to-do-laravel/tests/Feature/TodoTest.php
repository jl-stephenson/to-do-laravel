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
        $testData = ['name' => 'Test task', 'category_id' => 1];

        $response = $this->postJson('/api/todos', $testData);
        $response->assertStatus(200)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'success']);
            });

        $this->assertDatabaseHas('todos', $testData);
    }

    public function test_createTodo_failureCategoryOutOfRange(): void
    {
        $testData = ['name' => 'Test task', 'category_id' => 8];

        $response = $this->postJson('/api/todos', $testData);
        $response->assertStatus(422)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });
    }

    public function test_createTodo_failureNameOverMax(): void
    {
        $testData = ['name' => 'TesttesttestTesttesttestTesttesttestTesttesttestTesttesttestTesttesttestTesttesttestTesttesttestTesttesttestTesttesttest', 'category_id' => 1];
        $response = $this->postJson('/api/todos', $testData);
        $response->assertStatus(422)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });
    }


    public function test_createTodo_failureNameRequired(): void
    {
        $testData = ['category_id' => 1];

        $response = $this->postJson('/api/todos', $testData);
        $response->assertStatus(422)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });
    }

    public function test_createTodo_failureCategoryRequired(): void
    {
        $testData = ['name' => 'Clean fridge'];

        $response = $this->postJson('/api/todos', $testData);
        $response->assertStatus(422)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });
    }

    public function test_createTodo_malformedName(): void
    {
        $testData = ['name' => 12, 'category_id' => 2];

        $response = $this->postJson('/api/todos', $testData);
        $response->assertStatus(422)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });
    }

    public function test_createTodo_malformedCategory(): void
    {
        $testData = ['name' => 'Build shed', 'category_id' => 'Good'];

        $response = $this->postJson('/api/todos', $testData);
        $response->assertStatus(422)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });
    }

    public function test_editTodo_success(): void
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

    public function test_editTodo_failureCategoryOutOfRange(): void
    {
        Todo::factory()->create();

        $testData = ['name' => 'Test task', 'completed' => false, 'category_id' => 8];

        $response = $this->putJson('/api/todos/1', $testData);
        $response->assertStatus(422)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });
    }

    public function test_editTodo_failureNameOverMax(): void
    {
        Todo::factory()->create();

        $testData = ['name' => 'TesttesttestTesttesttestTesttesttestTesttesttestTesttesttestTesttesttestTesttesttestTesttesttestTesttesttestTesttesttest', 'completed' => false, 'category_id' => 1];

        $response = $this->putJson('/api/todos/1', $testData);
        $response->assertStatus(422)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });
    }

    public function test_editTodo_failureNameRequired(): void
    {
        Todo::factory()->create();

        $testData = ['completed' => false, 'category' => 1];

        $response = $this->putJson('/api/todos/1', $testData);
        $response->assertStatus(422)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });
    }

    public function test_editTodo_failureCompletedRequired(): void
    {
        Todo::factory()->create();

        $testData = ['name' => 'Test task', 'category_id' => 1];

        $response = $this->putJson('/api/todos/1', $testData);
        $response->assertStatus(422)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });
    }

    public function test_editTodo_failureCategoryRequired(): void
    {
        Todo::factory()->create();

        $testData = ['name' => 'Test task', 'completed' => false];

        $response = $this->putJson('/api/todos/1', $testData);
        $response->assertStatus(422)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });
    }

    public function test_editTodo_malformedName(): void
    {
        Todo::factory()->create();

        $testData = ['name' => 10, 'completed' => false, 'category_id' => 2];

        $response = $this->putJson('/api/todos/1', $testData);
        $response->assertStatus(422)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });
    }

    public function test_editTodo_malformedCompleted(): void
    {
        Todo::factory()->create();

        $testData = ['name' => 'Test task', 'completed' => 'no', 'category_id' => 2];

        $response = $this->putJson('/api/todos/1', $testData);
        $response->assertStatus(422)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });
    }

    public function test_editTodo_malformedCategory(): void
    {
        Todo::factory()->create();

        $testData = ['name' => 'Test task', 'completed' => false, 'category_id' => 'Yes'];

        $response = $this->putJson('/api/todos/1', $testData);
        $response->assertStatus(422)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });
    }

    public function test_deleteTodo_success(): void
    {
        Todo::factory()->create();

        $response = $this->delete('api/todos/1');
        $response->assertStatus(200)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'success']);
            });

        $this->assertDatabaseEmpty('todos');
    }

    public function test_deleteTodo_failureIdOutOfRange(): void
    {
        Todo::factory()->create();

        $response = $this->delete('api/todos/999999');
        $response->assertStatus(400)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'success']);
            });
    }
}


