<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\Country;
use Illuminate\Support\Facades\Validator;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;

class CartController extends Controller
{
    // إضافة منتج إلى العربة
    public function addToCart(Request $request)
    {

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


                $status = true;
                $message = '<strong>' . $product->title . '</strong> added in your cart successfully.';
                session()->flash('success', $message);
            } else {

                $status = false;
                $message = $product->title . ' already added in cart';

           }

      } else {
        // cart isa empty
       Cart::add($product->id, $product->title,1, $product->price, ['productImage' => (!empty($product->product_images)) ? $product->product_images->first(): '']);
        $status = true;
         $message = '<strong>'.$product->title . '</strong> added in your cart successfully.';
            session()->flash('success', $message);

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
   // dd($cartContent);
      $data['cartContent'] = $cartContent;
        return view('front.cart',$data);
    }

    // تحديث عنصر في العربة
    public function updateCart(Request $request)
{
    $rowId = $request->rowId;
    $qty = $request->qty;

    // الحصول على معلومات المنتج من السلة
    $itemInfo = Cart::get($rowId);
    $product = Product::find($itemInfo->id);

    if ($product->track_qty === 'Yes') { // إذا كان المنتج يتتبع الكمية
        if ($qty <= $product->qty) {
            Cart::update($rowId, $qty);
            $message = 'Cart updated successfully';
            $status = true;
            session()->flash('success', $message);
        } else {
            $message = 'Requested qty (' . $qty . ') not available in stock .';
            $status = false;
            session()->flash('error', $message);
        }
    } else { // إذا كان المنتج لا يتتبع الكمية
        Cart::update($rowId, $qty);
        $message = 'Cart updated successfully';
        $status = true;
        session()->flash('success', $message);
    }

    // استجابة JSON
    return response()->json([
        'status' => $status,
        'message' => $message,
    ]);
}


    // حذف عنصر من العربة
   public function deleteItem(Request $request)
{
    $rowId = $request->rowId;

    // التحقق من وجود العنصر في السلة
    $itemInfo = Cart::get($rowId);
    if ($itemInfo === null) {
        $errorMessage = 'Item not found in cart';
        session()->flash('error', $errorMessage);
        return response()->json([
            'status' => false,
            'message' => $errorMessage,
        ]);
    }

    // حذف العنصر من السلة
    Cart::remove($rowId);

    $successMessage = 'Item removed from cart successfully';
    session()->flash('success', $successMessage);

    // إرجاع الاستجابة
    return response()->json([
        'status' => true,
        'message' => $successMessage,
    ]);
   }
    public function checkout()
    {
        // التحقق من أن العربة ليست فارغة
        if (Cart::count() == 0) {
            return redirect()->route('front.cart')->with('error', 'Your cart is empty.');
        }

        //if user not logged in then redirect to login page

        if (Auth::check() == false) {

            if(!session()->has('url.intended')) {
                session(['url.intended' => url()->current()]);

           }

            return redirect()->route('account.login')->with('error', 'You need to login to proceed to checkout.');
        }

        session()->forget('url.intended');
        // استرجاع محتويات العربة ومجموع السعر

        $countries = Country::orderBy('name','ASC')->get();
        // تمرير البيانات إلى الصفحة
        return view('front.checkout', [

            'countries' => $countries
        ]);
    }

    public function processCheckout(Request $request){

        // Apply Validation
        $validator = Validator::make($request->all(),[
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'country' => 'required|exists:countries,id', // تأكد من أن الـ country موجودة في قاعدة البيانات
            'address' => 'required|string|max:500',
            'appartment' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip' => 'required|string|max:20',
            'mobile' => 'required|string|max:15',
        ]);
        if ($validator->fails()){
         return response()->json([
          'message' => 'Please fix the erroes',
           'status' => false,
           'errors' => $validator->errors()


         ]);


        }

        // save user address
      // $customerAddress =  CustomerAddress::find() ;
        $user = Auth::user();

        CustomerAddress::updateOrCreate(
            ['user_id'=> $user->id],
            [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'country_id' => $request->country,
                'address' => $request->address,
                'appartment' => $request->appartment,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip,
                'mobile' => $request->mobile

                
            ]

            
            );

            // store data in orders table

    if ($request->payment_method == 'cod') {
      
        $shipping = 0;
        $discount = 0;
        $subTotal = Cart::subtotal(2,'.','');
        $grandTotal = $subTotal+$shipping;


        
        $order = new Order;
        $order->subtotal = $subTotal;
            $order->shipping = $shipping;
            $order->grand_total = $grandTotal;

            // إنشاء الطلب
            $order->user_id = $user->id;
            $order->first_name = $request->first_name;
            $order->last_name = $request->last_name;
            $order->email = $request->email;
            $order->country_id = $request->country;
            $order->address = $request->address;
            $order->appartment = $request->appartment;
            $order->city = $request->city;
            $order->state = $request->state;
            $order->zip = $request->zip;
            $order->mobile = $request->mobile;
            $order->notes = $request->notes;

            $order->save();


            // store order items in order items table
            foreach (Cart::content() as $item) {
                $orderItem = new OrderItem;
                $orderItem->order_id = $order->id;  // ربط العنصر بالطلب
                $orderItem->product_id = $item->id; // تخزين الـ product_id
                $orderItem->name = $item->name;  // تخزين الكمية
                $orderItem->price = $item->price;
                $orderItem->qty = $item->qty;  // تخزين الكمية
                $orderItem->total = $item->subtotal; // تخزين السعر الإجمالي (الكمية * السعر)
                $orderItem->save(); // حفظ العنصر في الجدول
            }
            
    }else{


        
    }

            
    }
    
}
    