<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    //
    protected $connection = "mysql";
    protected $table = "calls";
    protected $fillable = ['log_crontab_id', 'call_id', 'date', 'duration', 'recording_url', 'status', 'phone', 'channel_id', 'is_duplicated', 'location', 'created_at_calls', 'updated_at_calls', 'client_number', 'call_uuid', 'call_mapped', 'status_log'];

    public function log_crontab()
    {
        return $this->belongsTo("Log_crontab.php");
    }
}
