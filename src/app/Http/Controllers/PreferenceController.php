<?php

namespace App\Http\Controllers;

use App\Models\Preference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Get(
 *     path="/api/preferences",
 *     summary="List user preferences",
 *     tags={"Preferences"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of preferences",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Preference"),
 *             example={
 *                 {"id":1,"user_id":1,"category":"Tech","source":"Guardian","author":"John Doe","created_at":"2024-01-01T00:00:00Z","updated_at":"2024-01-01T00:00:00Z"}
 *             }
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 */
class PreferenceController extends Controller
{
    public function index()
    {
        $preferences = Auth::user()->preferences;
        return response()->json($preferences);
    }

    /**
     * @OA\Post(
     *     path="/api/preferences",
     *     summary="Create a user preference",
     *     tags={"Preferences"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Preference")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Preference created",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Preference",
     *             example={"id":1,"user_id":1,"category":"Tech","source":"Guardian","author":"John Doe","created_at":"2024-01-01T00:00:00Z","updated_at":"2024-01-01T00:00:00Z"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
        ]);
        $preference = Auth::user()->preferences()->create($validated);
        return response()->json($preference, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/preferences/{id}",
     *     summary="Update a user preference",
     *     tags={"Preferences"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Preference")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Preference updated",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Preference",
     *             example={"id":1,"user_id":1,"category":"Tech","source":"Guardian","author":"John Doe","created_at":"2024-01-01T00:00:00Z","updated_at":"2024-01-01T00:00:00Z"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function update(Request $request, $id)
    {
        $preference = Auth::user()->preferences()->findOrFail($id);
        $validated = $request->validate([
            'category' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
        ]);
        $preference->update($validated);
        return response()->json($preference);
    }

    /**
     * @OA\Delete(
     *     path="/api/preferences/{id}",
     *     summary="Delete a user preference",
     *     tags={"Preferences"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Preference deleted",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Preference deleted successfully"))
     *     ),
     *     @OA\Response(response=404, description="Preference not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function destroy($id)
    {
        $preference = Auth::user()->preferences()->findOrFail($id);
        $preference->delete();
        return response()->json(['message' => 'Preference deleted successfully']);
    }
} 