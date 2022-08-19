<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use Concerns\UsesUuid;
    
    public static function create($from, $content)
    {
        $event = new self;
        $event->from = $from;
        $event->content = $content;
        $event->save();
        return $event;
    }
}
