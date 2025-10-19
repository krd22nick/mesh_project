<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;

class Controller
{

    public function redis()
    {
        var_dump('test redis');

        Redis::set('name', 'Taylor');

        var_dump( Redis::get('name') );
    }

}
