<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Post;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        
        return redirect('/');
    }

    public function showResult(Request $request, $id)
    {
        $data = [];

        $post = Post::with('reactions', 'shares')->find($id);

        if(!$post){
            $data['error'] = "Không tìm thấy dữ liệu yêu cầu";
            return view("resultSearch", $data);
        }

        $data['post'] = $post;
        $reactions = $post->reactions;

        $reactionsByCategories = [];
        foreach ($reactions as $reaction) {
            if(!array_key_exists($reaction['type'], $reactionsByCategories)){
                $reactionsByCategories[$reaction['type']] = 
                [
                    'meta' => \App\ReactionType::find($reaction['type']),
                    'data' => [
                        'eligible' => [],
                        'ineligible' => [],
                        'not_like' => []
                    ]
                ];
            }

            if( !$reaction->fbuser->is_eligible ){
                array_push($reactionsByCategories[$reaction['type']]['data']['ineligible'], $reaction);
            }
            else if( !$reaction->fbuser->is_like_fanpage ){
                array_push($reactionsByCategories[$reaction['type']]['data']['not_like'], $reaction);
            }else{
                array_push($reactionsByCategories[$reaction['type']]['data']['eligible'], $reaction);
            }
        }

        $shares = $post->shares;

        $data['title'] = 'Kết quả của '.$post->name;
        $data['reactionsByCategories'] = $reactionsByCategories;
        $data['reactionTypes'] = \App\ReactionType::orderBy('order')->get();
        $data['shares'] = $shares;
        return view("result", $data);
        
    }
}