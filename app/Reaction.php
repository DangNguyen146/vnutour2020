<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    use Concerns\UsesUuid;

    public function post()
    {
        return $this->belongsTo("\App\Post");
    }

    public function fbuser()
    {
        return $this->belongsTo("\App\Fbuser");
    }

    public function meta()
    {
        return $this->belongsTo('\App\ReactionType', 'type', 'id');
    }

    public function getPoint()
    {
        switch ($this->type) {
            case 'like':
                return 1;

            case 'love':
            case 'haha':
            case 'wow':
            case 'sad':
            case 'angry':
            case 'care':
                return 2;

            default:
                return 0;
                break;
        }
    }
}
