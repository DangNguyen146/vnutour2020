<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Concerns\UsesUuid;

    protected $casts = [
        'updated_at' => 'datetime',
    ];

    public function reactions()
    {
        return $this->hasMany("\App\Reaction");
    }

    public function shares()
    {
        return $this->hasMany("\App\Share");
    }

    public function fbusers()
    {
        return $this->hasManyThrough('\App\Fbuser', '\App\Reaction');
    }

    public function caclPoint()
    {
        $reactions = $this->reactions()->with('fbuser')->get();
        $total = 0;
        foreach ($reactions as $reaction) {
            if ( $reaction->fbuser->is_eligible){ //$reaction->fbuser->is_like_fanpage &&
                $total += $reaction->getPoint();
            }
        }

        $total += $this->shared * 3;
        return $total;
    }

    public function caclReactionPoint()
    {
        $reactions = $this->reactions()->with('fbuser')->get();
        $total = 0;
        foreach ($reactions as $reaction) {
            if ($reaction->fbuser->is_eligible){ //$reaction->fbuser->is_like_fanpage &&
                $total += $reaction->getPoint();
            }
        }

        return $total;
    }

    static public function updateRanking()
    {
        $posts = self::orderBy("point", "desc")->get();

        for ($i=0; $i < count($posts); $i++) { 
            $posts[$i]->ranking = $i+1;
            $posts[$i]->save();
        }
    }

}
