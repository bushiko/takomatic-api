<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
  protected $hidden = array('created_at', 'updated_at');
}
