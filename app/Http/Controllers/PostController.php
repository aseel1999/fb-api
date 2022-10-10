<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Friend;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
       $user=Auth::guard('sanctum')->user();
        $friends=Friend::friendships();
        dd($friends);
}
}
