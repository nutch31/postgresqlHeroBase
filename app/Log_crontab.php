<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log_crontab extends Model
{
    //
    protected $connection = "mysql";
    protected $table = "log_crontabs";
    protected $fillable = ['type', 'skip', 'take', 'status', 'remark'];

    public function calls()
    {
        return $this->hasMany('Call');
    }

    public function forms()
    {
        return $this->hasMany('Form');
    }
}
