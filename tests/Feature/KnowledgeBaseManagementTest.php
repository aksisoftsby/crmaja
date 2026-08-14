<?php

namespace Tests\Feature;

use App\Models\KbArticle;
use App\Models\KbCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KnowledgeBaseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_super_admin_can_create_category_and_article(): void
    {
        $admin = User::query()->where('email', 'admin@aksisoft.test')->firstOrFail();

        $this->actingAs($admin)->post('/kb-categories', ['name' => 'Panduan Awal'])->assertRedirect();
        $this->assertDatabaseHas('kb_categories', ['name' => 'Panduan Awal']);

        $categoryId = (int) KbCategory::query()->where('name', 'Panduan Awal')->value('id');
        $this->actingAs($admin)->post('/kb-articles', [
            'category_id' => $categoryId,
            'title' => 'Cara memulai',
            'content' => 'Langkah pertama untuk menggunakan CRM.',
            'is_published' => true,
            'is_client_only' => false,
        ])->assertRedirect();

        $article = KbArticle::query()->where('title', 'Cara memulai')->firstOrFail();
        $this->assertTrue($article->is_published);
        $this->assertNotEmpty($article->slug);

        $this->actingAs($admin)->get("/kb-articles/{$article->id}")->assertOk();
        $this->assertDatabaseHas('kb_articles', ['id' => $article->id, 'views_count' => 1]);
    }
}
