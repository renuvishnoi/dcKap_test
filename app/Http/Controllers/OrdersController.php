<?php

namespace App\Http\Controllers;

use App\Cart_model;
use App\Orders_model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class OrdersController extends Controller
{
    public function index(){
        $session_id=Session::get('session_id');
        $cart_datas=Cart_model::where('session_id',$session_id)->get();
        $total_price=0;
        foreach ($cart_datas as $cart_data){
            $total_price+=$cart_data->price*$cart_data->quantity;
        }
        $shipping_address=DB::table('delivery_address')->where('users_id',Auth::id())->first();
        return view('checkout.review_order',compact('shipping_address','cart_datas','total_price'));
    }
    public function order(Request $request){
        $input_data=$request->all();
        $payment_method=$input_data['payment_method'];
        Orders_model::create($input_data);
        if($payment_method=="COD"){
            $inputToCart=request()->ip();
            $delete_item=DB::table('cart')->where('ip_address',$inputToCart)->delete();
            // $delete_item->delete();
            return redirect('/cod');
        }
    }
    public function cod(){
        $user_order=Orders_model::where('users_id',Auth::id())->first();
        return view('payment.cod',compact('user_order'));
    }

}
