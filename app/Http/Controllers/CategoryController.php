<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use stdClass;

class CategoryController extends Controller
{
    /**
     * Create a new CategoryController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function createCategory(Request $request)
    {

        if (!Gate::allows('admin', auth()->user())) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string',
            'parent_id' => 'integer|nullable'
        ]);

        if ($request->parent_id) {
            $parent = Category::find($request->parent_id);
            if (!$parent) {
                abort(403);
            }
            if ($parent->level() >= env('CATEGORY_LEVEL_LIMIT')) {
                abort(403);
            }
        }

        $category = Category::create([
            'title' => $request->title,
            'parent_id' => $request->parent_id
        ]);

        return $this->returnData($category);
    }

    public function updateCategory(Request $request)
    {

        if (!Gate::allows('admin', auth()->user())) {
            abort(403);
        }

        $request->validate([
            'id' => 'required|integer',
            'title' => 'required|string',
            'parent_id' => 'integer|nullable'
        ]);

        if ($request->parent_id) {
            $parent = Category::find($request->parent_id);
            if (!$parent) {
                abort(403);
            }
            if ($parent->level() >= env('CATEGORY_LEVEL_LIMIT')) {
                abort(403);
            }
        }

        if ($request->parent_id == $request->id) {
            abort(403);
        }

        $category = Category::find($request->id);

        if (!$category) {
            abort(404);
        }

        $category->title = $request->title;
        $category->parent_id = $request->parent_id;
        $category->save();

        return $this->returnData($category);
    }

    public function getCategory(Request $request)
    {

        if (!Gate::allows('admin', auth()->user())) {
            abort(403);
        }

        $request->validate([
            'id' => 'required|integer'
        ]);

        $category = Category::find($request->id);

        if (!$category) {
            abort(404);
        }

        return $this->returnData($category);
    }

    public function getCategories(Request $request)
    {

        if (!Gate::allows('admin', auth()->user())) {
            abort(403);
        }

        $categories1 = Category::where('parent_id', null)->get();
        $category1IDs = [];

        foreach ($categories1 as $category) {
            array_push($category1IDs, $category->id);
            $category->subcategories = [];
        }

        $categories2 = Category::whereIn('parent_id', $category1IDs)->get();
        $category2IDs = [];

        foreach ($categories2 as $category) {
            array_push($category2IDs, $category->id);
            $category->subcategories = [];
        }

        $categories3 = Category::whereIn('parent_id', $category2IDs)->get();

        foreach ($categories2 as $category2) {
            foreach ($categories3 as $category3) {
                if ($category3->parent_id == $category2->id) {
                    $category2->subcategories = array_merge($category2->subcategories, [$category3]);
                }
            }
        }

        foreach ($categories1 as $category1) {
            foreach ($categories2 as $category2) {
                if ($category2->parent_id == $category1->id) {
                    $category1->subcategories = array_merge($category1->subcategories, [$category2]);
                }
            }
        }

        // $categories = Category::paginate(5);

        return $this->returnData($categories1);
    }

    public function deleteCategory(Request $request)
    {

        if (!Gate::allows('admin', auth()->user())) {
            abort(403);
        }

        $request->validate([
            'id' => 'required|integer'
        ]);

        $category = Category::find($request->id);

        if (!$category) {
            abort(404);
        }

        $category->delete();

        return $this->returnData($category);
    }
}
