<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Mail; 

class ContactController extends Controller
{
    //shoin the contact form
    public function create(){
        return view('contact.contact_us');
    }

    // creating the contact form 
    public function store(Request $request){
        //check if the user is authinticated
        if (!Auth::check()) {
            return redirect()->back()
                             ->withErrors(['auth_required' => 'You must be logged in to send us a message.'])
                             ->withInput(); // Keep their input if any was submitted
        }
        // User is authenticated, proceed.
        $user = Auth::user();

        

        // Validate the request data
        $validator = Validator::make($request->all(), [
            // 'form_submitted' => 'required',
            'contact_username' => 'required| min:5|max:50',
            'email' => 'required|email',
            'subject' => 'required|min:5|max:100',
            'category' => 'required',
            'message' => 'required|min:10|max:500',
        ]);
        //if any of the validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $errors = [];
        if ($request->input('contact_username') !== $user->name) {
            $errors['contact_username'] = 'The provided name does not match the logged-in user.';
        }
        
        if ($request->input('email') !== $user->email) {
            $errors['email'] = 'The provided email does not match the logged-in user.';
        }
        
        // If there are any errors, redirect back with all errors at once
        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        // Create a new contact instance
        $contact = Contact::create([
            'user_id' => $user->user_id,
            'username' => $request->input('contact_username'),
            'email' => $request->input('email'),
            'subject' => $request->input('subject'),
            'message_category' => $request->input('category'),
            'message' => $request->input('message'),
        ]);
        
        return redirect()->back()->with('success', 'Your message has been sent successfully!');

        // Save the contact to the database
        // $contact->save()->with('success', 'We Reseved your Message!, Will look into it and replay to you with an email');;

        // Send email notification to the user
        // Mail::to($contact->email)->send(new \App\Mail\ContactFormSubmitted($contact));

        // return redirect()->back()->with('success', 'Your message has been sent successfully!');
    }
}