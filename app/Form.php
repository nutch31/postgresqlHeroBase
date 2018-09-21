<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    //
    protected $connection = "mysql";
    protected $table = "forms";
    protected $fillable = ['log_crontab_id', 'form_id', 'channel_id', 'name', 'email', 'phone', 'custom_attributes', 'is_duplicated', 'ip', 'location', 'created_at_forms', 'updated_at_forms', 'page_url', 'status_log'];
    
    public function log_crontab()
    {
        return $this->belongsTo("Log_crontab.php");
    }
}
