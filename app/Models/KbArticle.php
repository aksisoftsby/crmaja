<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class KbArticle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['category_id', 'title', 'slug', 'content', 'is_published', 'is_client_only', 'views_count', 'created_by'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean', 'is_client_only' => 'boolean', 'views_count' => 'integer'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(KbCategory::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
