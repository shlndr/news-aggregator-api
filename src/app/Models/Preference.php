<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *   schema="Preference",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="Preference ID"),
 *   @OA\Property(property="user_id", type="integer", description="User ID"),
 *   @OA\Property(property="category", type="string", description="Preferred category"),
 *   @OA\Property(property="source", type="string", description="Preferred news source"),
 *   @OA\Property(property="author", type="string", description="Preferred author"),
 *   @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
 *   @OA\Property(property="updated_at", type="string", format="date-time", description="Update timestamp")
 * )
 */

class Preference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category',
        'source',
        'author',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
