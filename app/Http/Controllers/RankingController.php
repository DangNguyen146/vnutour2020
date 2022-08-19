<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Post;

class RankingController extends Controller
{
    public function index()
    {        
        $posts = Post::orderBy("ranking")->get();

        $data = [
            'posts' => $posts,
            'title' => 'Bảng xếp hạng',
            'subtitle' => 'Hall of Fame'
        ];

        $data['reactionTypes'] = \App\ReactionType::orderBy('order')->get();

        return view("ranking", $data);
    }
}
