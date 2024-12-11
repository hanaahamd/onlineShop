<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Models\Product;

class CartController extends Controller
{
    // إضافة منتج إلى العربة
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::with('product_images')->find($request->product_id);

       // Cart::add($product->id, $product->name, $request->quantity, $product->price);
        if($product == null) {
        return response()->json([
             'status' => false,          
            'message' => 'Record not Found'   
        ]);
      }
      
      if (cart::count() > 0){
            //echo "Product already in cart";
            //Products found in cart
            // Check if this product already in the cart
            // Return as message that product already added in your cart
            // if product not found in the cart, then add product in cart 

        $cartContent = Cart::content();
        $ProductAlreadyExist = false;

        foreach ($cartContent as $item) {
          if ($item->id == $product->id){
            $ProductAlreadyExist = true;
            
          }
           
        }
           if
            ($ProductAlreadyExist == false){
                Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => (!empty
                ($product->product_images)) ? $product->product_images->first() : '']);


                $status = false;
                $message = $product->title . ' added in cart';
           } else {

                $status = false;
                $message = $product->title . ' already added in cart';

           }
        
      } else {
        // cart isa empty
       Cart::add($product->id, $product->title,1, $product->price, ['productImage' => (!empty($product->product_images)) ? $product->product_images->first(): '']);
        $status = true;
         $message = $product->title.'added in cart';

       
      }
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
     
     }
    
        
        // عرض محتويات العربة()
    public function cart()
    {
     $cartContent = Cart::content();
    //dd($cartContent);
      $data['cartContent'] = $cartContent;
        return view('front.cart',$data);
    }

    // // تحديث عنصر في العربة
    // public function update(Request $request, $rowId)
    // {
    //     $request->validate([
    //         'quantity' => 'required|integer|min:1',
    //     ]);

    //     Cart::update($rowId, $request->quantity);

    //     return response()->json(["message" => "تم تحديث الكمية بنجاح!"]);
    // }

    // // حذف عنصر من العربة
    // public function destroy($rowId)
    // {
    //     Cart::remove($rowId);

    //     return response()->json(["message" => "تم حذف المنتج من العربة بنجاح!"]);
    // }
}