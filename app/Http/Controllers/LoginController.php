<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function create()
    {
        // dd(session()->all()); 
        return view('login-subscribe.login_subscripe', ['activeTab' => 'login']);
    }
    public function store(Request $request)
    {

        //validation for the login form
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', Password::min(8)->letters()->numbers()->max(64)],
        ]);

        //attempt to log the user in
        if(!Auth::attempt($validated)){
            throw ValidationException::withMessages([
                'email' => 'The provided credentials do not match our records.',
            ])->status(422);
        } 

        //regenerate the session token
        request()->session()->regenerate();

        //redirect the user to the home page
        return redirect('/');
    } 

    public function destroy()
    {
        Auth::logout();

        return redirect('/');
    }
}
