<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    /**
     * Create a new CommentController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function createComment(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'text' => 'required|string',
            'product_id' => 'integer|nullable'
        ]);

        // return auth()->user()->id;

        $comment = Comment::create([
            'title' => $request->title,
            'text' => $request->text,
            'user_id' => auth()->user()->id,
            'product_id' => $request->product_id
        ]);

        return $this->returnData($comment);
    }

    public function updateComment(Request $request)
    {

        if (!Gate::allows('moderator', auth()->user()) && !Gate::allows('admin', auth()->user())) {
            abort(403);
        }

        $request->validate([
            'id' => 'required|integer',
            'title' => 'required|string',
            'text' => 'required|string'
        ]);

        $comment = Comment::find($request->id);

        if (!$comment) {
            abort(404);
        }

        $comment->title = $request->title;
        $comment->text = $request->text;
        $comment->save();

        return $this->returnData($comment);
    }

    public function getComment(Request $request)
    {

        if (!Gate::allows('moderator', auth()->user()) && !Gate::allows('admin', auth()->user())) {
            abort(403);
        }

        $request->validate([
            'id' => 'required|integer'
        ]);

        $comment = Comment::find($request->id);

        if (!$comment) {
            abort(404);
        }

        return $this->returnData($comment);
    }

    public function getComments(Request $request)
    {

        if (!Gate::allows('moderator', auth()->user()) && !Gate::allows('admin', auth()->user())) {
            abort(403);
        }

        $comments = Comment::paginate(5);

        return $this->returnData($comments);
    }

    public function deleteComment(Request $request)
    {

        if (!Gate::allows('moderator', auth()->user()) && !Gate::allows('admin', auth()->user())) {
            abort(403);
        }

        $request->validate([
            'id' => 'required|integer'
        ]);

        $comment = Comment::find($request->id);

        if (!$comment) {
            abort(404);
        }

        $comment->delete();

        return $this->returnData($comment);
    }
}
