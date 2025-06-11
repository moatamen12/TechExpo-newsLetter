<?php
namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{  
    // Store top-level comment
    public function store(Request $request, $article_id)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,comment_id'
        ]);

        $comment = new Comment();
        $comment->user_id = Auth::id();
        $comment->article_id = $article_id;
        $comment->content = $request->input('comment');
        $comment->parent_id = $request->input('parent_id'); // Will be null for top-level comments
        $comment->save();

        return redirect()->back()->with('success', 'Comment added successfully!');
    }
}

