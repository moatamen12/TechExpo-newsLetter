<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\userSavedArticle;
use App\Models\User;
use App\Models\userFollower;
use Illuminate\Support\Facades\Auth;


class ProfilesController extends Controller
{

    public function index()
    {
        return view('profile.profile');
    }
}