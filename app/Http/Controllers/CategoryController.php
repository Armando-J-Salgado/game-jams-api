<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use AuthorizesRequests;
    public function __construct() {
        $this->authorizeResource(Category::class);
    }
    /**
     * Display a listing of the resource.
     */
    /**
     * List Categories
     *
     * Display a listing of the categories.
     *
     * @group Categories
     * @authenticated
     *
     * @response 200 {
     *  "data": [
     *    {
     *      "id": 1,
     *      "name": "Game Jam 2026"
     *    }
     *  ]
     * }
     * @response 401 {
     *  "message": "Unauthenticated."
     * }
     * @response 403 {
     *  "message": "This action is unauthorized."
     * }
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Category::class);
        $categories = Category::all();
        return CategoryResource::collection($categories);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Create Category
     *
     * Store a newly created category in the database.
     *
     * @group Categories
     * @authenticated
     *
     * @bodyParam name string required The name of the category. Example: Art
     *
     * @response 201 {
     *  "id": 1,
     *  "name": "Art"
     * }
     * @response 401 {
     *  "message": "Unauthenticated."
     * }
     * @response 403 {
     *  "message": "This action is unauthorized."
     * }
     * @response 422 {
     *  "message": "The given data was invalid.",
     *  "errors": {"name": ["The name field is required."]}
     * }
     */
    public function store(StoreCategoryRequest $request)
    {
        $this->authorize('create', Category::class);
        $data = $request->validated();
        $category = Category::create($data);
        return response()->json(CategoryResource::make($category), 201);
    }

    /**
     * Display the specified resource.
     */
    /**
     * Get Category
     *
     * Display the specified category.
     *
     * @group Categories
     * @authenticated
     *
     * @urlParam category int required The ID of the category. Example: 1
     *
     * @response 200 {
     *  "id": 1,
     *  "name": "Art"
     * }
     * @response 401 {
     *  "message": "Unauthenticated."
     * }
     * @response 403 {
     *  "message": "This action is unauthorized."
     * }
     * @response 404 {
     *  "message": "No query results for model [App\\Models\\Category]."
     * }
     */
    public function show(Category $category)
    {
        $this->authorize('view', $category);
        return CategoryResource::make($category);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update Category
     *
     * Update the specified category in storage. Applies to both PUT and PATCH requests.
     *
     * @group Categories
     * @authenticated
     *
     * @urlParam category int required The ID of the category. Example: 1
     * @bodyParam name string required The new name for the category. Example: 2D Art
     *
     * @response 200 {
     *  "id": 1,
     *  "name": "2D Art"
     * }
     * @response 401 {
     *  "message": "Unauthenticated."
     * }
     * @response 403 {
     *  "message": "This action is unauthorized."
     * }
     * @response 404 {
     *  "message": "There are no matches for the searched category"
     * }
     * @response 422 {
     *  "message": "The given data was invalid.",
     *  "errors": {"name": ["The name field is required."]}
     * }
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $this->authorize('update', $category);
        $data = $request->validated();
        $category->update($data);
        return response()->json(CategoryResource::make($category));
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Delete Category
     *
     * Remove the specified category from storage.
     *
     * @group Categories
     * @authenticated
     *
     * @urlParam category int required The ID of the category. Example: 1
     *
     * @response 200 {
     *  "message": "Category deleted successfully"
     * }
     * @response 401 {
     *  "message": "Unauthenticated."
     * }
     * @response 403 {
     *  "message": "This action is unauthorized."
     * }
     * @response 404 {
     *  "message": "No query results for model [App\\Models\\Category]."
     * }
     */
    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
