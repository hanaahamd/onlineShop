<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

  class AuthController extends Controller
  {
// تسجيل مستخدم جديد
   public function register(Request $request)
   {
     return view('front.account.register');

   }

// تسجيل الدخول
   public function login()
   {
     return view('front.account.login');
  }

  
  public function processRegister(Request $request){


   $validator = Validator::make($request->all(),[

   'name' => 'required|string|max:255',
    'email' => 'required|email|unique:users,email',
    'password' => 'required|string|min:5|confirmed',
    ]);

  if ($validator->passes()) {

   $user = new User;
   $user->name = $request->name;
    $user->email = $request->email;
   $user->phone = $request->phone;
    $user->password = Hash::make($request->password);
   $user->save();


   session()->flash('sucess','You have been registered successfully.');

  return response()->json([
  'status' => true
 ]);

    } else {
     return response()->json([
      'status' => false,
      'errors' => $validator->errors(),
      ]);
    }
  }
  public function authenticate(Request $request)
    {
        // تحقق من صحة البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // إذا فشل التحقق من البيانات المدخلة
        if ($validator->passes()) {
          
        // محاولة تسجيل الدخول
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {


        if (session()->has('url.intended')) {
          return redirect(session()->get('url.intended'));
        }

          
            return redirect()->route('account.profile');
        } else {
            //session()->flash('error', 'Either email or password is incorrect.');
       
            return redirect()->route('account.login')
                ->withInput($request->only('email'))
                ->with('error', 'Either email or password is incorrect.');
        }
      
        } else { 
       return redirect()->route('account.login')
        ->withErrors($validator)
        ->withInput($request->only('email'));
    }
   }

    public function profile(){
        return view('front.account.profile');
   }

public function logout(){
   Auth::logout();
   return redirect()->route('account.login')
   ->with('success', 'You successfully logged out');
    
}
   
}