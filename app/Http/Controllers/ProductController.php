<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    /**
     * Create a new ProductController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function createProduct(Request $request)
    {
        if (!Gate::allows('admin', $request->user)) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|integer',
            'categories' => 'required|array',
            'categories.*' => 'integer',
            'images' => 'required|array',
            'images.*' => 'integer',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::create([
                'title' => $request->title,
                'description' => $request->description,
                'price' => $request->price,
            ]);

            $product->categories()->attach($request->categories);

            foreach ($request->images as $key => $image) {
                if ($key == 0) {
                    $product->images()->attach($image, ['main' => true]);
                } else {
                    $product->images()->attach($image, ['main' => false]);
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        DB::commit();

        $product = Product::with('categories', 'images')->find($product->id);

        return $this->returnData($product);
    }

    public function updateProduct(Request $request)
    {

        if (!Gate::allows('admin', auth()->user())) {
            abort(403);
        }

        $request->validate([
            'id' => 'required|integer',
            'title' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|integer',
            'categories' => 'required|array',
            'categories.*' => 'integer',
            'images' => 'required|array',
            'images.*' => 'integer',
        ]);

        $product = Product::find($request->id);

        if (!$product) {
            abort(404);
        }

        DB::beginTransaction();

        try {
            $product->title = $request->title;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->save();

            $product->categories()->detach();
            $product->images()->detach();

            $product->categories()->attach($request->categories);

            foreach ($request->images as $key => $image) {
                if ($key == 0) {
                    $product->images()->attach($image, ['main' => true]);
                } else {
                    $product->images()->attach($image, ['main' => false]);
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        DB::commit();

        $product = Product::with('categories')->find($request->id);

        return $this->returnData($product);
    }

    public function getProduct(Request $request)
    {

        if (!Gate::allows('admin', auth()->user())) {
            abort(403);
        }

        $request->validate([
            'id' => 'required|integer'
        ]);

        $product = Product::with('categories', 'images', 'comments')->find($request->id);

        if (!$product) {
            abort(404);
        }

        return $this->returnData($product);
    }

    public function getProducts(Request $request)
    {

        if (!Gate::allows('admin', auth()->user())) {
            abort(403);
        }

        $products = Product::with('categories', 'images', 'comments')->paginate(5);

        return $this->returnData($products);
    }

    public function deleteProduct(Request $request)
    {

        if (!Gate::allows('admin', auth()->user())) {
            abort(403);
        }

        $request->validate([
            'id' => 'required|integer'
        ]);

        $product = Product::find($request->id);

        if (!$product) {
            abort(404);
        }

        DB::beginTransaction();

        try {
            $product->categories()->detach();
            $product->images()->detach();
            $product->delete();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        DB::commit();

        return $this->returnData($product);
    }
}
