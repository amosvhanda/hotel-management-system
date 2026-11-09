<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogCategory;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use App\Models\BlogPost;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth; 

class CommentController extends Controller
{
    public function StoreComment(Request $request){

        $request->validate([
            'post_id' => 'required|exists:blog_posts,id',
            'message' => 'required|string|min:3|max:2000',
        ]);

        // The comment always belongs to the signed-in user (never trust a user id from the form).
        Comment::insert([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'post_id' => $request->post_id,
            'message' => $request->message,
            'status' => '0',
            'created_at' => Carbon::now(),
        ]);

        $post = \App\Models\BlogPost::find($request->post_id);
        \App\Support\StaffAlerts::send(
            'comment_new',
            'New blog comment from ' . \Illuminate\Support\Facades\Auth::user()->name . ' on "' . \Illuminate\Support\Str::limit($post->post_titile ?? 'a post', 40) . '"',
            route('all.comment'),
            'bx-comment-detail'
        );

        $notification = array(
            'message' => 'Thank you! Your comment will appear once it has been approved.',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification); 

    }// End Method 


    public function AllComment(){

        $allcomment = Comment::latest()->get();
        return view('backend.comment.all_comment',compact('allcomment'));

    }// End Method 


    public function UpdateCommentStatus(Request $request){

        $commentId = $request->input('comment_id');
        $isChecked = $request->input('is_checked',0);

        $comment = Comment::find($commentId);
        if ($comment) {
           $comment->status = $isChecked;
           $comment->save(); 
        }

        return response()->json(['message' => 'Comment Status Updated Successfully']);

    }// End Method 





}
