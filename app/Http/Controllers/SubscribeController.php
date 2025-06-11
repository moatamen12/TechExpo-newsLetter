<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;

class SubscribeController extends Controller
{
    public function create()
    {
        return view('login-subscribe.login_subscripe', ['activeTab' => 'subscribe']);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'min:5','max:20'],
            'Subemail' => ['required', 'email', 'unique:users,email', 'max:255'], 
            'Subpassword' => ['required', 'confirmed', Password::min(8)->letters()->numbers()->max(64)],
            'Subpassword_confirmation' => ['required'],
            // 'terms' => 'required|accepted', 
        ];
        $validator = Validator::make($request->all(), $rules);

        // if validation fails redirect to subscribe
        if ($validator->fails()) {
            return redirect()->route('subscribe') 
                ->withErrors($validator) 
                ->withInput(); 
        }

        // Get validated data if validation passes
        $validated = $validator->validated();


        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['Subemail'],
            'password' => bcrypt($validated['Subpassword']),
            // 'terms' => $validated['terms'],
        ]);

        Auth::login($user);

        return redirect('/');
    } 
}
