<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Newsletter;
use App\Models\UserProfiles;
use Illuminate\Support\Facades\Auth;

class NewsLetterController extends Controller
{
    //get the current user profile id
    public function getCorrentUser():int
    {
        $user = Auth::id();
        $profile_id = UserProfiles::where('user_id', $user)->first()->profile_id;
        return $profile_id;
    }

    public function newsletter(Request $request){
        $profile_id = $this->getCorrentUser();
        
        $newsletters = Newsletter::with('author')
            ->where('author_id', $profile_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        if ($request) {
            $newsletters->appends($request->query());
        }
            
        return view('dashboard.newsletter.newsletter', compact('newsletters'));
    }
    
    public function show($id)
    {
        $newsletter = Newsletter::with('author')->findOrFail($id);
        return view('dashboard.newsletter.show', compact('newsletter'));
    }
    
    // Helper methods for filtering (similar to DashboardController)
    public function sentNewsletters($user_id, $limit = 10, Request $request = null)
    {   
        $newsletters = Newsletter::with('author')
            ->where('author_id', $user_id)
            ->where('status', 'sent')
            ->orderBy('sent_at', 'desc')
            ->paginate($limit);
        
        if ($request) {
            $newsletters->appends($request->query());
        }
        
        return $newsletters;
    }

    public function scheduledNewsletters($user_id, $limit = 10, Request $request = null)
    {   
        $newsletters = Newsletter::with('author')
            ->where('author_id', $user_id)
            ->where('status', 'scheduled')
            ->orderBy('scheduled_at', 'asc') // Show earliest scheduled first
            ->paginate($limit);
        
        if ($request) {
            $newsletters->appends($request->query());
        }
        
        return $newsletters;
    }

    public function draftNewsletters($user_id, $limit = 10, Request $request = null)
    {   
        $newsletters = Newsletter::with('author')
            ->where('author_id', $user_id)
            ->where('status', 'draft')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
            
        if ($request) {
            $newsletters->appends($request->query());
        }
        
        return $newsletters;
    }
}
