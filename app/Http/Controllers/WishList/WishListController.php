<?php

namespace App\Http\Controllers\WishList;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use App\Models\Product;
// use App\Models\User;
use App\Models\WishList;

class WishListController extends Controller
{
    public function store(Request $request){
        
        // $product = Product::findorFail($request->product_id);
        // $user = User::findorFail($request->user_id);
        // $product->wishlists()->create();
        // $user->wishlists()->create();

        // return "succesfull";

        $wishlist = WishList::create([
            'user_id' => $request->user_id,
            'product_id' => $request->product_id,
        ]);

        return response()->json(['message' => 'Added to wishlist successfully!', 'data' => $wishlist], 201);
    }

    public function index(){
        return WishList::with(['user', 'product'])->get();
    }
}
