<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\ShoppingCart;
use Illuminate\Pagination\LengthAwarePaginator;

class UserController extends Controller
{   
    public function mypage()
    {
        $user = Auth::user();

        return view('users.mypage', compact('user'));
    }

   
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $user = Auth::user();

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $user = Auth::user();

        $user->name = $request->input('name') ? $request->input('name') : $user->name;
        $user->email = $request->input('email') ? $request->input('email') : $user->email;
        $user->postal_code = $request->input('postal_code') ? $request->input('postal_code') : $user->postal_code;
        $user->address = $request->input('address') ? $request->input('address') : $user->address;
        $user->phone = $request->input('phone') ? $request->input('phone') : $user->phone;
        $user->update();

        return to_route('mypage');
    }

    public function update_password(Request $request)
    {
        $validatedData = $request->validate([
            'password' => 'required|confirmed',
        ]);

        $user = Auth::user();

        if ($request->input('password') == $request->input('password_confirmation')) {
            $user->password = bcrypt($request->input('password'));
            $user->update();
        } else {
            return to_route('mypage.edit_password');
        }

        return to_route('mypage');
    }

    public function edit_password()
    {
        return view('users.edit_password');
    }

    public function favorite()
    {
        $user = Auth::user();

        $favorite_products = $user->favorite_products;

        return view('users.favorite', compact('favorite_products'));
    }

    public function destroy(Request $request)
    {
        Auth::user()->delete();
        return redirect('/');
    }

    public function cart_history_index(Request $request)
    {
        $page = $request->page != null ? $request->page : 1;
        $user_id = Auth::user()->id;
        $billings = ShoppingCart::getCurrentUserOrders($user_id);
        $total = count($billings);
        $billings = new LengthAwarePaginator(array_slice($billings, ($page - 1) * 15, 15), $total, 15, $page, array('path' => $request->url()));

        return view('users.cart_history_index', compact('billings', 'total'));
    }

    public function cart_history_show(Request $request)
    {
        $num = $request->num;
        $user_id = Auth::user()->id;
        $cart_info = DB::table('shoppingcart')->where('instance', $user_id)->where('number', $num)->get()->first();
        Cart::instance($user_id)->restore($cart_info->identifier);
        $cart_contents = Cart::content();
        Cart::instance($user_id)->store($cart_info->identifier);
        Cart::destroy();

        DB::table('shoppingcart')->where('instance', $user_id)
            ->where('number', null)
            ->update(
                [
                    'code' => $cart_info->code,
                    'number' => $num,
                    'price_total' => $cart_info->price_total,
                    'qty' => $cart_info->qty,
                    'buy_flag' => $cart_info->buy_flag,
                    'updated_at' => $cart_info->updated_at
                ]
            );

        return view('users.cart_history_show', compact('cart_contents', 'cart_info'));
    }

}