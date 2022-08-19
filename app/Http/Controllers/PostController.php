<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Post;
use \App\Fbuser;
use \App\Reaction;
use \App\Share;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::orderBy('updated_at')->get();
        return [
            "data" => $posts
            
        ];
    }

    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if(!$post){
            abort(404);
        }

        $reaction_data = [];
        $jobs = [];
        //Process reactions
        if ($request->has('reactions')){
            $reactions = $request->input('reactions');

            if (is_string($reactions)){
                $reactions = json_decode($reactions, true);
            }

            # Delete old reactions on database
            Reaction::where("post_id", $post->id)->delete();
            $post -> like = 0;
            $post -> haha = 0;
            $post -> wow = 0;
            $post -> sad = 0;
            $post -> angry = 0;
            $post -> care = 0;
            
            foreach ($reactions as $reaction) {
                $fbuser = Fbuser::where('fburl',$reaction['uri'])->first();

                if (!$fbuser){
                    $fbuser = new Fbuser();
                    $fbuser->fburl = $reaction['uri'];
                    $fbuser->is_like_fanpage = $reaction['is_liked'];
                    $fbuser->is_eligible = true;
                    $fbuser->name = $reaction['name'];
                    $fbuser->save();
                }

                $fbuser->is_like_fanpage = $reaction['is_liked'];
                $fbuser->save();

                if(! array_key_exists($reaction['type'], $reaction_data)){
                    $reaction_data[$reaction['type']] = 0;
                }
                $reaction_db = new Reaction();
                $reaction_db -> post_id = $post->id;
                $reaction_db -> type = $reaction['type'];
                $reaction_db ->fbuser_id = $fbuser->id;
                $reaction_db -> active = true;
                $reaction_db -> save();

                if ($reaction['is_liked'] || true){ //Rules: Must like page
                    $reaction_data[$reaction['type']] += 1;
                }

                foreach ($reaction_data as $type => $count) {
                    $post->$type = $count;
                }
            }
        }

        if ($request->has('sharings')){
            $sharings = $request->input('sharings');

            if (is_string($sharings)){
                $sharings = json_decode($sharings, true);
            }

            //Delete old sharings on db
            Share::where('post_id', $post->id)->delete();

            foreach($sharings as $sharing){
                $fbuser = Fbuser::where('fburl',$sharing['uri'])->first();

                if (!$fbuser){
                    $fbuser = new Fbuser();
                    $fbuser->fburl = $sharing['uri'];
                    $fbuser->is_eligible = true;
                    $fbuser->name = $sharing['name'];
                    $fbuser->save();
                }

                $sharing_obj = new Share();
                $sharing_obj -> post_id = $post->id;
                $sharing_obj -> fbuser_id = $fbuser->id;
                $sharing_obj->active = true;
                $sharing_obj->save();
            }

            $post->shared = count($sharings);
        }

        if($request->has('meta')){
            $meta = $request->input('meta');
            $post->type = $meta['type'];
            $post->image = $meta['image'];
        }

        $post -> point = $post->caclPoint();
        $post->save();
        Post::updateRanking();
        return [
            "success" => true,
            "message" => "New data was updated",
            "reaction_data" => $reaction_data,
            "jobs" => $jobs
        ];
    }
}
