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

    public function test_createTodo(): void
    {
        $testData = ['name' => 'Test task', 'completed' => false, 'category_id' => 1];

        $response = $this->postJson('/api/todos', $testData);
        $response->assertStatus(200)
            ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['message', 'success']);
            });

        $this->assertDatabaseHas('todos', $testData);
    }

}