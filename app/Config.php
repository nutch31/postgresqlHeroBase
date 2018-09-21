<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    //
    protected $connection = "mysql";
    protected $table = "configs";
}
