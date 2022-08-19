<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReactionType extends Model
{
    public function getPoint()
    {
        switch ($this->id) {
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

    protected $casts = ['id' => 'string'];
}
