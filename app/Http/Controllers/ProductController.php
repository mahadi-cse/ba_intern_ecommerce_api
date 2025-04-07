<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // public function index()
    // {
    //     return response()->json(Product::all());
    // }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $product = Product::create($validated);

        // Handle image uploads
        foreach ($request->file('images') as $key => $image) {
            $path = $image->store('product_images', 'public');
            
            $product->images()->create([
                'path' => $path,
                'is_primary' => $key === 0 // First image is primary
            ]);
        }

        return response()->json([
            'product' => $product,
            'images' => $product->images
        ], 201);
    }

    public function index()
    {
        return response()->json(Product::with('images')->get());
    }

    public function show(Product $product)
    {
        return response()->json($product->load('images'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'new_images' => 'sometimes|array',
            'new_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'deleted_images' => 'sometimes|array',
            'deleted_images.*' => 'exists:product_images,id'
        ]);

        $product->update($validated);

        // Handle deleted images
        if ($request->has('deleted_images')) {
            $imagesToDelete = $product->images()->whereIn('id', $request->deleted_images)->get();
            
            foreach ($imagesToDelete as $image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }
        }

        // Handle new images
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $image) {
                $path = $image->store('product_images', 'public');
                
                $product->images()->create([
                    'path' => $path,
                    'is_primary' => $product->images()->count() === 0
                ]);
            }
        }

        return response()->json($product->load('images'));
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(null, 204);
    }
}