<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Share extends Model
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
}
