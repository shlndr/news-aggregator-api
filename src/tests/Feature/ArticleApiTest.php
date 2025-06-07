<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class ArticleApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_crud_articles()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
        $headers = [
            'Accept' => 'application/json',
        ];

        // Create
        $data = [
            'title' => 'Test Article',
            'content' => 'Test content',
            'author' => 'Test Author',
            'published_at' => now()->toDateTimeString(),
        ];
        $create = $this->postJson('/api/articles', $data, $headers);
        $create->assertStatus(201)->assertJsonFragment(['title' => 'Test Article']);
        $articleId = $create->json('id');

        // List
        $list = $this->getJson('/api/articles', $headers);
        $list->assertStatus(200)->assertJsonFragment(['title' => 'Test Article']);

        // Show
        $show = $this->getJson('/api/articles/' . $articleId, $headers);
        $show->assertStatus(200)->assertJsonFragment(['title' => 'Test Article']);

        // Update
        $update = $this->putJson('/api/articles/' . $articleId, ['title' => 'Updated'], $headers);
        $update->assertStatus(200)->assertJsonFragment(['title' => 'Updated']);

        // Delete
        $delete = $this->deleteJson('/api/articles/' . $articleId, [], $headers);
        $delete->assertStatus(200)->assertJsonFragment(['message' => 'Article deleted successfully']);
    }
} 