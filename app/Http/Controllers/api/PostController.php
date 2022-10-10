<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Models\Friend;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       $user=Auth::guard('sanctum')->user();
       
        $friends=Friend::friendships();
        if($friends->isEmpty()){
            return new PostCollection($user->posts);
        }
        return new PostCollection(Post::whereIn('user_id',[$friends->pluck('user->id'),$friends->pluck('friend_id')]));

        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
               
        $post=$request->user()->posts()->create([
            'user_id'=>$request->user_id,
                'body'=>$request->body,
               ]);
               return new PostResource($post);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return new PostResource($post);
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
        $post = Post::findOrFail($id);
        $post->update($request()->all());
        return $post;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $user = Auth::guard('sanctum')->user();
       $post->delete();
       if ($post->comments ) {
        foreach ($post->comments as $comment) {
            $post->delete($comment);
        }
    }
       return [
        'message' => 'Post deleted',
       ];
    }
}