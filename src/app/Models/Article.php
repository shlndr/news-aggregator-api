<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *   schema="Article",
 *   type="object",
 *   required={"title", "content", "author"},
 *   @OA\Property(property="id", type="integer", description="Article ID"),
 *   @OA\Property(property="title", type="string", description="Title of the article"),
 *   @OA\Property(property="content", type="string", description="Content of the article"),
 *   @OA\Property(property="author", type="string", description="Author of the article"),
 *   @OA\Property(property="published_at", type="string", format="date-time", description="Publication date"),
 *   @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
 *   @OA\Property(property="updated_at", type="string", format="date-time", description="Update timestamp")
 * )
 */

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'author',
        'published_at',
    ];
}
