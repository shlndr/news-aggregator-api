<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Get(
 *     path="/api/articles",
 *     summary="List articles",
 *     tags={"Articles"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(name="search", in="query", description="Search term", required=false, @OA\Schema(type="string")),
 *     @OA\Parameter(name="author", in="query", description="Filter by author", required=false, @OA\Schema(type="string")),
 *     @OA\Parameter(name="published_at", in="query", description="Filter by date", required=false, @OA\Schema(type="string", format="date")),
 *     @OA\Response(
 *         response=200,
 *         description="Successful response",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Article")),
 *             @OA\Property(property="meta", type="object"),
 *             @OA\Property(property="links", type="object"),
 *             example={
 *                 "data":{
 *                     {"id":1,"title":"Sample","content":"Text","author":"Author","published_at":"2024-01-01T00:00:00Z","created_at":"2024-01-01T00:00:00Z","updated_at":"2024-01-01T00:00:00Z"}
 *                 },
 *                 "meta":{},
 *                 "links":{}
 *             }
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=429, description="Too Many Requests")
 * )
 */
class ArticleController extends Controller
{
    /**
     * Display a listing of the resource with pagination, search, and filtering.
     */
    public function index(Request $request)
    {
        $cacheKey = 'articles_index_' . md5(json_encode($request->all()));
        $articles = Cache::remember($cacheKey, 600, function () use ($request) {
            $query = Article::query();

            // Search by title or content
            if ($search = $request->query('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'ILIKE', "%$search%")
                      ->orWhere('content', 'ILIKE', "%$search%")
                      ->orWhere('author', 'ILIKE', "%$search%") ;
                });
            }

            // Filter by author
            if ($author = $request->query('author')) {
                $query->where('author', $author);
            }

            // Filter by published_at (date)
            if ($published = $request->query('published_at')) {
                $query->whereDate('published_at', $published);
            }

            return $query->orderByDesc('published_at')->paginate(10);
        });
        return response()->json($articles);
    }

    /**
     * @OA\Post(
     *     path="/api/articles",
     *     summary="Create an article",
     *     tags={"Articles"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Article")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Article created successfully",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Article",
     *             example={"id":1,"title":"Sample","content":"Text","author":"Author","published_at":"2024-01-01T00:00:00Z","created_at":"2024-01-01T00:00:00Z","updated_at":"2024-01-01T00:00:00Z"}
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
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author' => 'required|string|max:255',
            'published_at' => 'nullable|date',
        ]);
        $article = Article::create($validated);
        return response()->json($article, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     summary="Get article details",
     *     tags={"Articles"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Article ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article details",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Article",
     *             example={"id":1,"title":"Sample","content":"Text","author":"Author","published_at":"2024-01-01T00:00:00Z","created_at":"2024-01-01T00:00:00Z","updated_at":"2024-01-01T00:00:00Z"}
     *         )
     *     ),
     *     @OA\Response(response=404, description="Article not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function show($id)
    {
        $article = Article::findOrFail($id);
        return response()->json($article);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'author' => 'sometimes|required|string|max:255',
            'published_at' => 'nullable|date',
        ]);
        $article->update($validated);
        return response()->json($article);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();
        return response()->json(['message' => 'Article deleted successfully']);
    }

    /**
     * @OA\Get(
     *     path="/api/feed/personalized",
     *     summary="Get personalized article feed based on user preferences",
     *     tags={"Articles"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Personalized article feed",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Article")),
     *             @OA\Property(property="meta", type="object"),
     *             @OA\Property(property="links", type="object"),
     *             example={
     *                 "data":{
     *                     {"id":1,"title":"Sample","content":"Text","author":"Author","published_at":"2024-01-01T00:00:00Z","created_at":"2024-01-01T00:00:00Z","updated_at":"2024-01-01T00:00:00Z"}
     *                 },
     *                 "meta":{},
     *                 "links":{}
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=429, description="Too Many Requests")
     * )
     */
    public function personalized(Request $request)
    {
        $user = $request->user();
        $cacheKey = 'articles_personalized_' . $user->id;
        $articles = Cache::remember($cacheKey, 600, function () use ($user) {
            $preferences = $user->preferences;
            $query = Article::query();
            $categories = $preferences->pluck('category')->filter()->unique()->toArray();
            $sources = $preferences->pluck('source')->filter()->unique()->toArray();
            $authors = $preferences->pluck('author')->filter()->unique()->toArray();

            if (!empty($categories)) {
                $query->whereIn('title', $categories);
            }
            if (!empty($sources)) {
                $query->whereIn('author', $sources);
            }
            if (!empty($authors)) {
                $query->orWhereIn('author', $authors);
            }

            return $query->orderByDesc('published_at')->paginate(10);
        });
        return response()->json($articles);
    }

    /**
     * @OA\Get(
     *     path="/api/articles/personalized/feed",
     *     summary="Get personalized news feed based on user preferences",
     *     tags={"Articles"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Personalized feed",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Article")),
     *             @OA\Property(property="meta", type="object"),
     *             @OA\Property(property="links", type="object"),
     *             example={
     *                 "data":{
     *                     {"id":1,"title":"Sample","content":"Text","author":"Author","published_at":"2024-01-01T00:00:00Z","created_at":"2024-01-01T00:00:00Z","updated_at":"2024-01-01T00:00:00Z"}
     *                 },
     *                 "meta":{},
     *                 "links":{}
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
}
