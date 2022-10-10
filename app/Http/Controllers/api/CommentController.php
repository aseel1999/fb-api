<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Models\Post;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $data= $this->validate($request, [
            'post_id' => 'exists:posts,id|numeric',
            'body' => 'required'
        ]);
        $comment=$request->user()->posts('comments')->create([
            'post_id'=>$data['post_id'],
            'body'=>$data['body'],
            'user_id'=>Auth::user()->id,
           ]);
           return $comment;

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'body'=>['sometimes','required','text'],
        ]);
        $request->merge([
            'post_id'=>'required',
            'user_id'=>'required',
        ]);
        $comment = Comment::findOrFail($id);
        $comment->update($request()->all());
        return $comment;
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        $user = Auth::guard('sanctum')->user();
       $comment->delete();
    }

    }