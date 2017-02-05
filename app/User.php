<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
  protected $hidden = ['created_at', 'updated_at'];

  protected $casts = ['lat' => 'float', 'lng' => 'float'];

  public function route()
  {
  	return $this->hasOne('App\Route');
  }
}
