<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RouteStep extends Model
{
	protected $casts = ['lat' => 'float', 'lng' => 'float'];
	
    public function route()
    {
    	$this->belongsTo('App\Route');
    }
}
