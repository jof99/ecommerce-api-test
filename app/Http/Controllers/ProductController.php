<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CartItem;
use GrahamCampbell\ResultType\Success;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user_id=$request->user()->id;
        $products=Product::where('user_id',$user_id)->get();
        return response($products,201);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validate the request data for a single product
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required|max:255',
            'price' => 'required|numeric'
        ]);
    
        $user_id = $request->user()->id;
    
        // create the product
        Product::create([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'user_id' => $user_id,
        ]);
    
        return response([
            'success' => true,
            'message' => 'Product created successfully'
        ], 201);
        
    }
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product=Product::findOrFail($id);
        return response($product,201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product=Product::findOrFail($id);
        $product->update([
            'title'=>$request->title,
            'description'=>$request->description,
            'price'=>$request->price,
            'user_id'=>$request->user()->id
        ]);

        return response([
            'success'=>true,
            'message'=>'Product updated sucessfully'
        ],201);


    }
    public function uploadProducts(Request $request)
    {
        // validate the request data for multiple products
        $request->validate([
            'products' => 'required|array|min:1',
            'products.*.title' => 'required|max:255',
            'products.*.description' => 'required|max:255',
            'products.*.price' => 'required|numeric'
        ]);
    
        $user_id = $request->user()->id;
    
        // create each product
        foreach ($request->input('products') as $productData) {
            Product::create([
                'title' => $productData['title'],
                'description' => $productData['description'],
                'price' => $productData['price'],
                'user_id' => $user_id,
            ]);
        }
    
        return response([
            'success' => true,
            'message' => 'Products uploaded successfully'
        ], 201);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product=Product::where('id',$id)->delete();

        return response([
            'success'=>true,
            'message'=>'Product deleted sucessfully'
        ],201);
    }

    public function addToCart(Request $request)
{
    $user_id = $request->user()->id;

    $product_id = $request->input('product_id');
    $quantity = $request->input('quantity');

    $product = Product::find($product_id);
    if (!$product) {
        return response(['error' => 'Product not found'], 404);
    }

    $cart_item = CartItem::where('user_id', $user_id)
                          ->where('product_id', $product_id)
                          ->first();

    if ($cart_item) {
        $cart_item->quantity += $quantity;
        $cart_item->save();
    } else {
        $cart_item = new CartItem([
            'user_id' => $user_id,
            'product_id' => $product_id,
            'quantity' => $quantity
        ]);
        $cart_item->save();
    }

    return response([
        'success' => true,
        'message' => 'Product added to cart'
    ], 201);
}
public function getCart(Request $request)
{
    $user = $request->user();
    $cartItems = $user->cartItems()->with('product')->get();
    return response()->json([
        'success' => true,
        'items' => $cartItems
    ]);
}
}
