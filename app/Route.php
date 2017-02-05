<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    public function steps()
    {
    	return $this->hasMany('App\RouteStep');
    }
}
