<?php

namespace App;
use DB;
use App\Call;
use App\Form;
use App\Config;
use App\Log_crontab;

use Illuminate\Database\Eloquent\Model;

class Herobase extends Model
{
    //
    public function Crontab_call()
    {
        set_time_limit(3600);

        $config = Config::where('type', '=', 'calls')->first();

        $running = 0; 

        $log_crontabs = new Log_Crontab;

        $log_crontabs->type = $config->type;
        $log_crontabs->skip = $config->skip;
        $log_crontabs->take = $config->take;
        $log_crontabs->remark = '';
        $log_crontabs->status = 1;

        $log_crontabs->save();
      
        //tracking_phone = DID Phone, phone = customer phone
        $calls = DB::table("calls")
                ->join('channels', 'channels.id', '=', 'calls.channel_id')
                ->select(
                    "calls.id",  "calls.date", "calls.duration", "calls.recording_url", "calls.status", "calls.phone",
                    "calls.channel_id", "calls.is_duplicated", "calls.location", "calls.created_at", "calls.updated_at", "calls.client_number",
                    "calls.call_uuid", "calls.call_mapped", 
                    "channels.tracking_phone"
                )
                ->orderBy('calls.id', 'asc')
                ->skip($config->skip)
                ->take($config->take)
                ->get();
                    
        foreach($calls as $call)
        {

            $count_call_local = Call::where("call_id", "=", $call->id)->where('status_log', '=', '1')->count();
            
            if($count_call_local == 0)
            {                         
                $call_local = Call::create([
                        'log_crontab_id' => $log_crontabs->id, 'call_id' => $call->id, 'date' => $call->date, 'duration' => $call->duration,
                        'recording_url' => $call->recording_url, 'status' => $call->status, 'phone' =>$call->phone,
                        'channel_id' => $call->channel_id, 'is_duplicated' => $call->is_duplicated, 'location' => $call->location,
                        'created_at_calls' => $call->created_at, 'updated_at_calls' => $call->updated_at, 
                        'client_number' => $call->client_number,'call_uuid' => $call->call_uuid, 'call_mapped' => $call->call_mapped,
                        'status_log' => 0
                ]);
                            
                if($call->status == 1)
                {
                    $status = "ANSWER";
                }
                else if($call->status == 2)
                {
                    $status = "MISSED CALL";
                }
            
                $submitDateTime = str_replace(" ","T",$call->date."+0700");
                $timestamp = strtotime($submitDateTime);
            
                $ch = curl_init("https://palakorn.com/hlws/pbxLeads");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, true);
                    
                $data = array(
                            'duration' => $call->duration,
                            'status' =>  $status,
                            'recordingUrl' =>  $call->recording_url,
                            'heroNumber' => $call->tracking_phone,
                            'callerId' => $call->phone,
                            'timestamp' => $timestamp                
                        );
                    
                $data = json_encode($data);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                $output = curl_exec($ch);
                $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
            
                if($info==201)
                {
                    $call_local_update = Call::find($call_local->id);
            
                    $call_local_update->status_response = $info;
                    $call_local_update->comment = $output;
                    $call_local_update->status_log = 1;
            
                    $call_local_update->save();
                }
                else
                {
                    $call_local_update = Call::find($call_local->id);
            
                    $call_local_update->status_response = $info;
                    $call_local_update->comment = $output;
            
                    $call_local_update->save();
                }

                echo $info."<BR>";
            
                curl_close($ch);  
            }

            $running++;
        }
        
        $config = Config::find($config->id);

        $config->skip = $config->skip + $running;

        $config->save();

    }

    public function Crontab_form()
    {
        set_time_limit(3600);

        $config = Config::where('type', '=', 'forms')->first();

        $running = 0; 

        $log_crontabs = new Log_Crontab;

        $log_crontabs->type = $config->type;
        $log_crontabs->skip = $config->skip;
        $log_crontabs->take = $config->take;
        $log_crontabs->remark = '';
        $log_crontabs->status = 1;

        $log_crontabs->save();
        
        $forms = DB::table("forms")
                ->join('channels', 'channels.id', '=', 'forms.channel_id')
                ->select(
                    "forms.id", "forms.channel_id", "forms.name", "forms.email", "forms.phone", "forms.custom_attributes", 
                    "forms.is_duplicated", "forms.ip", "forms.location", "forms.created_at", "forms.updated_at", "forms.page_url", 
                    "channels.adwords_campaign_id", "channels.facebook_campaign_id"
                )
                ->orderBy('forms.id', 'asc')
                ->skip($config->skip)
                ->take($config->take)
                ->get();

                //->whereNotNull('channels.adwords_campaign_id')->whereNotNull('channels.adwords_campaign_id')
                
        foreach($forms as $form)
        {
                    
            $count_form_local = Form::where("form_id", "=", $form->id)->where('status_log', '=', '1')->count();
        
            if($count_form_local == 0)
            {                         
                $form_local = Form::create([
                    'form_id' => $form->id, 'log_crontab_id' => $log_crontabs->id, 'channel_id' => $form->channel_id,
                    'name' => $form->name, 'email' => $form->email, 'phone' =>$form->phone,
                    'custom_attributes' => $form->custom_attributes, 'is_duplicated' => $form->is_duplicated, 
                    'ip' => $form->ip, 'location' => $form->location,
                    'created_at_forms' => $form->created_at, 'updated_at_forms' => $form->updated_at,
                    'page_url' => $form->page_url, 'status_log' => 0
                ]);
        
                if(!is_null($form->adwords_campaign_id))
                {
                    $analyticCampaignId = $form->adwords_campaign_id;
                }
                else
                {
                    $analyticCampaignId = $form->facebook_campaign_id;
                }
                        
                $array = explode(".", $form->created_at);
                $submitDateTime = str_replace(" ","T",$array[0]."+0700");                
        
                $array_name = explode(" ", $form->name);
                $count = count($array_name);
        
                $first_name = $array_name[0];
                $last_name = '';
        
                for($a=1;$a<$count;$a++)
                {
                    $last_name .= $array_name[$a].' ';
                }
        
                $last_name = substr($last_name, 0, -1);
        
                $ch = curl_init("https://palakorn.com/hlws/landingPageSubmit");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, true);
                
                $data = array(
                    'analyticCampaignId' => $analyticCampaignId,
                    'firstName' =>  $first_name,
                    'lastName' =>  $last_name,
                    'email' => $form->email,
                    'phone' => $form->phone,
                    'submitDateTime' => $submitDateTime                
                );
                
                $data = json_encode($data);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                $output = curl_exec($ch);
                $info = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
        
                if($info==201)
                {
                    $form_local_update = Form::find($form_local->id);
        
                    $form_local_update->status_response = $info;
                    $form_local_update->comment = $output;
                    $form_local_update->status_log = 1;
        
                    $form_local_update->save();
                }
                else
                {
                    $form_local_update = Form::find($form_local->id);
        
                    $form_local_update->status_response = $info;
                    $form_local_update->comment = $output;
        
                    $form_local_update->save();
                }
                
                echo $info."<BR>";
        
                curl_close($ch);  
            }

            $running++;
        }
        
        $config = Config::find($config->id);

        $config->skip = $config->skip + $running;

        $config->save();
    }

    public function Call($account_id, $channel_id, $limit, $offset)
    {
        set_time_limit(1200);

        $num = 0;

        $log_crontabs = new Log_Crontab;

        $log_crontabs->type = "Run Mannaul";
        $log_crontabs->skip = $offset;
        $log_crontabs->take = $limit;
        $log_crontabs->status = 1;

        $log_crontabs->save();

        //client_number = DID Phone, phone = customer phone
        $calls = DB::select("select ca.* 
        from accounts as a
        INNER JOIN campaigns cam On cam.account_id = a.id
        INNER JOIN channels ch On ch.campaign_id = cam.id
        INNER JOIN calls ca On ca.channel_id = ch.id
        where a.id = '".$account_id."' and ch.id = '".$channel_id."' order by ca.id asc limit ".$limit." OFFSET ".$offset." ");

        foreach($calls as $call)
        {
            /*
            echo 'id = '.$call->id."<BR>";
            echo 'duration = '.$call->duration."<BR>";
            echo 'recording_url = '.$call->recording_url."<BR>";
            echo 'status = '.$call->status."<BR>";
            echo 'phone = '.$call->phone."<BR>";
            echo 'channel_id = '.$call->channel_id."<BR>";
            echo 'is_duplicated = '.$call->is_duplicated."<BR>";
            echo 'location = '.$call->location."<BR>";
            echo 'created_at = '.$call->created_at."<BR>";
            echo 'updated_at = '.$call->updated_at."<BR>";
            echo 'client_number = '.$call->client_number."<BR>";
            echo 'call_uuid = '.$call->call_uuid."<BR>";
            echo 'call_mapped = '.$call->call_mapped."<BR>";
            */
            
            $count_call_local = Call::where("call_id", "=", $call->id)->where('status_log', '=', '1')->count();

            if($count_call_local == 0)
            {                         
                $call_local = Call::create([
                    'log_crontab_id' => $log_crontabs->id, 'call_id' => $call->id, 'date' => $call->date, 'duration' => $call->duration,
                    'recording_url' => $call->recording_url, 'status' => $call->status, 'phone' =>$call->phone,
                    'channel_id' => $call->channel_id, 'is_duplicated' => $call->is_duplicated, 'location' => $call->location,
                    'created_at_calls' => $call->created_at, 'updated_at_calls' => $call->updated_at, 
                    'client_number' => $call->client_number,'call_uuid' => $call->call_uuid, 'call_mapped' => $call->call_mapped,
                    'status_log' => 0
                ]);
                
                if($call->status == 1)
                {
                    $status = "ANSWER";
                }
                else if($call->status == 2)
                {
                    $status = "MISSED CALL";
                }

                $submitDateTime = str_replace(" ","T",$call->date."+0700");
                $timestamp = strtotime($submitDateTime);

                $ch = curl_init("https://palakorn.com/hlws/pbxLeads");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, true);
        
                $data = array(
                    'duration' => $call->duration,
                    'status' =>  $status,
                    'recordingUrl' =>  $call->recording_url,
                    'heroNumber' => $call->client_number,
                    'callerId' => $call->phone,
                    'timestamp' => $timestamp                
                );
        
                $data = json_encode($data);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                $output = curl_exec($ch);
                $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);  

                echo $info."<BR>";

                if($info==201)
                {
                    $num++;

                    $call_local_update = Call::find($call_local->id);

                    $call_local_update->status_log = 1;

                    $call_local_update->save();
                }

                curl_close($ch);  
            }
        }

        echo $num;
    }

    public function Form($account_id, $channel_id, $limit, $offset)
    {
        set_time_limit(1200);
        
        $num = 0;

        $log_crontabs = new Log_Crontab;

        $log_crontabs->type = "Run Mannaul";
        $log_crontabs->skip = $offset;
        $log_crontabs->take = $limit;
        $log_crontabs->status = 1;

        $log_crontabs->save();

        //client_number = DID Phone, phone = customer phone
        $forms = DB::select("select f.*, ch.adwords_campaign_id, ch.facebook_campaign_id
        from accounts as a
        INNER JOIN campaigns cam On cam.account_id = a.id
        INNER JOIN channels ch On ch.campaign_id = cam.id
        INNER JOIN forms f On f.channel_id = ch.id
        where a.id = '".$account_id."' and ch.id = '".$channel_id."'  order by f.id asc limit ".$limit." OFFSET ".$offset." ");

        foreach($forms as $form)
        {
            /*
            echo 'id = '.$form->id."<BR>";
            echo 'log_crontab_id = '.$log_crontabs->id."<BR>";
            echo 'channel_id = '.$form->channel_id."<BR>";
            echo 'name = '.$form->name."<BR>";
            echo 'email = '.$form->email."<BR>";
            echo 'phone = '.$form->phone."<BR>";
            echo 'custom_attributes = '.$form->custom_attributes."<BR>";
            echo 'is_duplicated = '.$form->is_duplicated."<BR>";
            echo 'ip = '.$form->ip."<BR>";
            echo 'location = '.$form->location."<BR>";
            echo 'created_at = '.$form->created_at."<BR>";
            echo 'updated_at = '.$form->updated_at."<BR>";
            echo 'page_url = '.$form->page_url."<BR>";
            */
            
            $count_form_local = Form::where("form_id", "=", $form->id)->where('status_log', '=', '1')->count();

            if($count_form_local == 0)
            {                         
                $form_local = Form::create([
                    'form_id' => $form->id, 'log_crontab_id' => $log_crontabs->id, 'channel_id' => $form->channel_id,
                    'name' => $form->name, 'email' => $form->email, 'phone' =>$form->phone,
                    'custom_attributes' => $form->custom_attributes, 'is_duplicated' => $form->is_duplicated, 
                    'ip' => $form->ip, 'location' => $form->location,
                    'created_at_forms' => $form->created_at, 'updated_at_forms' => $form->updated_at,
                    'page_url' => $form->page_url, 'status_log' => 0
                ]);

                if(!is_null($form->adwords_campaign_id))
                {
                    $analyticCampaignId = $form->adwords_campaign_id;
                }
                else
                {
                    $analyticCampaignId = $form->facebook_campaign_id;
                }
                
                $array = explode(".", $form->created_at);
                $submitDateTime = str_replace(" ","T",$array[0]."+0700");                

                $array_name = explode(" ", $form->name);
                $count = count($array_name);

                $first_name = $array_name[0];
                $last_name = '';

                for($a=1;$a<$count;$a++)
                {
                    $last_name .= $array_name[$a].' ';
                }

                $last_name = substr($last_name, 0, -1);

                $ch = curl_init("https://palakorn.com/hlws/landingPageSubmit");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, true);
        
                $data = array(
                    'analyticCampaignId' => $analyticCampaignId,
                    'firstName' =>  $first_name,
                    'lastName' =>  $last_name,
                    'email' => $form->email,
                    'phone' => $form->phone,
                    'submitDateTime' => $submitDateTime                
                );
        
                $data = json_encode($data);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                $output = curl_exec($ch);
                $info = curl_getinfo($ch, CURLINFO_HTTP_CODE); 

                echo $info."<BR>"; 

                if($info==201)
                {
                    $num++;

                    $form_local_update = Form::find($form_local->id);

                    $form_local_update->status_log = 1;

                    $form_local_update->save();
                }

                curl_close($ch);  
            }
        }

        echo $num;
    }
    
    public function SearchHeroBase($adwords_account_id, $facebook_account_id, $adwords_campaign_id, $facebook_campaign_id, $callfrom_start_date, $callfrom_end_date)
    { 
        if($adwords_account_id == "" && $facebook_account_id == "")
        {
            return response()->json('Required one for adwords_account_id or facebook_account_id', '400');
        }

        $resultAccounts = [];

        $accounts = DB::table('accounts')->select(
            'id', 'company_name', 'adwords_account_id', 'created_at', 
            'updated_at', 'facebook_account_id', 'currency', 'status'
        );
        
        if($adwords_account_id != '')
        {
            $accounts = $accounts->Orwhere('adwords_account_id', '=', $adwords_account_id);
        }
        if($facebook_account_id != '')
        {
            $accounts = $accounts->Orwhere('facebook_account_id', '=', $facebook_account_id);
        }
        $accounts = $accounts->orderBy('created_at')->get();      

        foreach($accounts as $accountKey => $account)
        {
            $resultAccounts['Account'][$accountKey] = [
				'account_id' => $account->id,
                'company_name' => $account->company_name,
                'adwords_account_id' => $account->adwords_account_id,
                'account_created_at' => $account->created_at,
                'account_updated_at' => $account->updated_at,
                'facebook_account_id' => $account->facebook_account_id,
                'currency' => $account->currency,
                'account_status' => $account->status
            ];
            
            $campaigns = DB::table('campaigns')->select('id', 'name', 'status', 'created_at', 'created_at', 'updated_at');
            $campaigns = $campaigns->where('account_id', '=', $account->id);
            $campaigns = $campaigns->orderBy('created_at')->get();            

            foreach($campaigns as $campaignKey => $campaign)
            {
                $channels = DB::table('channels')->select(
                    'id', 'adwords_campaign_id', 'kind', 'status', 'url', 'tracking_phone', 'forward_phone',
                    'facebook_campaign_id', 'name', 'daily_net_budget', 'daily_gross_budget', 'daily_min_leads',
                    'daily_max_leads', 'created_at', 'updated_at'
                );            
                $channels = $channels->where('campaign_id', '=', $campaign->id);
                if($adwords_campaign_id != "" && $facebook_campaign_id == "")
                {
                    $channels = $channels->where('adwords_campaign_id', '=', $adwords_campaign_id);
                }
                if($adwords_campaign_id == "" && $facebook_campaign_id != "") 
                {
                    $channels = $channels->where('facebook_campaign_id', '=', $facebook_campaign_id);
                }
                if($adwords_campaign_id != "" && $facebook_campaign_id != "")
                {
                    $channels = $channels->where('adwords_campaign_id', '=', $adwords_campaign_id)->Orwhere('facebook_campaign_id', '=', $facebook_campaign_id);
                }
                $channels = $channels->orderBy('created_at')->get();

                foreach($channels as $channelKey => $channel)
                {                    
                    if($channelKey == 0)
                    {
                        $resultAccounts['Account'][$accountKey]['Campaign'][$campaignKey] = [
                            'campaign_id' => $campaign->id,
                            'campaign_name' => $campaign->name,
                            'campaign_status' => $campaign->status,
                            'campaign_created_at' => $campaign->created_at,
                            'campaign_updated_at' => $campaign->updated_at
                        ];
                    }

                    $resultAccounts['Account'][$accountKey]['Campaign'][$campaignKey]['Channel'][$channelKey] = [
                        'channel_id' => $channel->id,
                        'channel_name' => $channel->name,
                        'adwords_campaign_id' => $channel->adwords_campaign_id,
                        'kind' => $channel->kind,
                        'channel_status' => $channel->status,
                        'url' => $channel->url,
                        'tracking_phone' => $channel->tracking_phone,
                        'forward_phone' => $channel->forward_phone,
                        'facebook_campaign_id' => $channel->facebook_campaign_id,
                        'daily_net_budget' => $channel->daily_net_budget,
                        'daily_gross_budget' => $channel->daily_gross_budget,
                        'daily_min_leads' => $channel->daily_min_leads,
                        'daily_max_leads' => $channel->daily_max_leads,
                        'channel_created_at' => $channel->created_at,
                        'channel_updated_at' => $channel->updated_at
                    ];
                                    
                    $calls = DB::table('calls')->select(
                        'id', 'date', 'duration', 'recording_url', 'status', 'phone'
                        , 'is_duplicated', 'location', 'client_number', 'call_uuid', 'created_at'
                    );
                    $calls = $calls->where('channel_id', '=', $channel->id);
                    if($callfrom_start_date != "" && $callfrom_end_date != "")
                    {
                         $calls = $calls->whereBetween('date', [$callfrom_start_date, $callfrom_end_date]);
                    }         
                    $calls = $calls->orderBy('created_at')->get(); 

                    $calls_lead_total = DB::table('calls')->where('channel_id', '=', $channel->id);
                    if($callfrom_start_date != "" && $callfrom_end_date != "")
                    {
                         $calls_lead_total = $calls_lead_total->whereBetween('date', [$callfrom_start_date, $callfrom_end_date]);
                    }         
                    $calls_lead_total = $calls_lead_total->orderBy('created_at')->get()->count(); 

                    $call_lead_real = DB::table('calls')->where('channel_id', '=', $channel->id)->where('is_duplicated', '=', 'false');
                    if($callfrom_start_date != "" && $callfrom_end_date != "")
                    {
                         $call_lead_real = $call_lead_real->whereBetween('date', [$callfrom_start_date, $callfrom_end_date]);
                    }         
                    $call_lead_real = $call_lead_real->orderBy('created_at')->get()->count(); 

                    $call_actuals = DB::table('calls')->selectRaw('MIN(date) as call_min_date, MAX(date) as call_max_date')->where('channel_id', '=', $channel->id);
                    if($callfrom_start_date != "" && $callfrom_end_date != "")
                    {
                         $call_actuals = $call_actuals->whereBetween('date', [$callfrom_start_date, $callfrom_end_date]);
                    }         
                    $call_actuals = $call_actuals->get();              
                    
                    foreach($call_actuals as $call_actual)
                    {
                        if($call_actual->call_min_date != "" && $call_actual->call_max_date != "")
                        {
                            $array = array(
                                'call_lead_total' => $calls_lead_total,
                                'call_lead_real' => $call_lead_real,
                                'call_actual_start_date' => $call_actual->call_min_date, 
                                'call_actual_end_date' => $call_actual->call_max_date
                            );
                            $resultAccounts['Account'][$accountKey]['Campaign'][$campaignKey]['Channel'][$channelKey] = array_merge($resultAccounts['Account'][$accountKey]['Campaign'][$campaignKey]['Channel'][$channelKey], $array);
                        }                        
                    }
                                        
                    foreach($calls as $callKey => $call)
                    {
                        $resultAccounts['Account'][$accountKey]['Campaign'][$campaignKey]['Channel'][$channelKey]['Call'][$callKey] = [
                            'call_id' => $call->id,
                            'call_date' => $call->date,
                            'duration' => $call->duration,
                            'recording_url' => $call->recording_url,
                            'call_status' => $call->status,
                            'phone' => $call->phone,
                            'is_duplicated' => $call->is_duplicated,
                            'location' => $call->location,
                            'client_number' => $call->client_number,
                            'call_uuid' => $call->call_uuid,
                            'call_created_at' => $call->created_at
                        ];
                    }

                    $forms = DB::table('forms')->select(
                        'id', 'name', 'email', 'phone', 'custom_attributes', 'is_duplicated', 
                        'ip', 'location', 'page_url', 'created_at'
                    );
                    $forms = $forms->where('channel_id', '=', $channel->id);
                    if($callfrom_start_date != "" && $callfrom_end_date != "")
                    {
                        $forms = $forms->whereBetween('created_at', [$callfrom_start_date, $callfrom_end_date]);
                    }         
                    $forms = $forms->orderBy('created_at')->get();   

                    $form_lead_total = DB::table('forms')->where('channel_id', '=', $channel->id);
                    if($callfrom_start_date != "" && $callfrom_end_date != "")
                    {
                         $form_lead_total = $form_lead_total->whereBetween('created_at', [$callfrom_start_date, $callfrom_end_date]);
                    }         
                    $form_lead_total = $form_lead_total->orderBy('created_at')->get()->count(); 

                    $form_lead_real = DB::table('forms')->where('channel_id', '=', $channel->id)->where('is_duplicated', '=', 'false');
                    if($callfrom_start_date != "" && $callfrom_end_date != "")
                    {
                         $form_lead_real = $form_lead_real->whereBetween('created_at', [$callfrom_start_date, $callfrom_end_date]);
                    }         
                    $form_lead_real = $form_lead_real->orderBy('created_at')->get()->count(); 
    
                    $form_actuals = DB::table('forms')->selectRaw('MIN(created_at) as form_min_date, MAX(created_at) as form_max_date')->where('channel_id', '=', $channel->id);
                    if($callfrom_start_date != "" && $callfrom_end_date != "")
                    {
                        $form_actuals = $form_actuals->whereBetween('created_at', [$callfrom_start_date, $callfrom_end_date]);
                    }         
                    $form_actuals = $form_actuals->get(); 
                    
                    foreach($form_actuals as $form_actual)
                    {
                        if($form_actual->form_min_date != "" && $form_actual->form_max_date != "")
                        {
                            $array = array(
                                'form_lead_total' => $form_lead_total,
                                'form_lead_real' => $form_lead_real,
                                'form_actual_start_date' => $form_actual->form_min_date, 
                                'form_actual_end_date' => $form_actual->form_max_date
                            );
                            $resultAccounts['Account'][$accountKey]['Campaign'][$campaignKey]['Channel'][$channelKey] = array_merge($resultAccounts['Account'][$accountKey]['Campaign'][$campaignKey]['Channel'][$channelKey], $array);
                        }       
                        
                        
                    }
                    
                    foreach($forms as $formKey => $form)
                    {
                        $resultAccounts['Account'][$accountKey]['Campaign'][$campaignKey]['Channel'][$channelKey]['Form'][$formKey] = [
                            'form_id' => $form->id,
                            'name' => $form->name,
                            'email' => $form->email,
                            'phone' => $form->phone,
                            'custom_attributes' => $form->custom_attributes,
                            'is_duplicated' => $form->is_duplicated,
                            'ip' => $form->ip,
                            'location' => $form->location,
                            'page_url' => $form->page_url,
                            'from_created_at' => $form->created_at
                        ];
                    }//end forms
                }//end channels
            }//end campaigns
        }//end accounts

        return $resultAccounts;
    }

    public function accountCall($account_id, $start_date, $end_date)
    {
        $result = DB::select("select a.id as account_id, a.company_name, a.adwords_account_id, a.created_at as account_created_at, a.updated_at as account_updated_at, a.facebook_account_id, a.currency, a.status as account_status, 
        cam.name as campaign_name, cam.status as campaign_status, cam.created_at as campaign_created_at, cam.updated_at as campaign_updated_at,
        ch.adwords_campaign_id, ch.kind, ch.status as channel_status, ch.url, ch.tracking_phone, ch.forward_phone, ch.facebook_campaign_id, ch.name as channel_name, ch.daily_net_budget, ch.daily_gross_budget, ch.daily_min_leads, ch.daily_max_leads, ch.created_at as channel_created_at, ch.updated_at as channel_updated_at,
        ca.date as call_date, ca.duration, ca.recording_url, ca.status as call_status, ca.phone, ca.is_duplicated, ca.location, ca.client_number, ca.call_uuid, ca.created_at as call_created_at
        from accounts as a
        INNER JOIN campaigns cam On cam.account_id = a.id
        INNER JOIN channels ch On ch.campaign_id = cam.id
        INNER JOIN calls ca On ca.channel_id = ch.id
        where a.id = '".$account_id."' and ca.date between '".$start_date."' and '".$end_date."' ");

        return $result;
    }
    
    public function accountForm($account_id, $start_date, $end_date)
    {
        $result = DB::select("select a.id as account_id, a.company_name, a.adwords_account_id, a.created_at as account_created_at, a.updated_at as account_updated_at, a.facebook_account_id, a.currency, a.status as account_status, 
        cam.name as campaign_name, cam.status as campaign_status, cam.created_at as campaign_created_at, cam.updated_at as campaign_updated_at,
        ch.adwords_campaign_id, ch.kind, ch.status as channel_status, ch.url, ch.tracking_phone, ch.forward_phone, ch.facebook_campaign_id, ch.name as channel_name, ch.daily_net_budget, ch.daily_gross_budget, ch.daily_min_leads, ch.daily_max_leads, ch.created_at as channel_created_at, ch.updated_at as channel_updated_at,
        f.name as customer_name, f.email as customer_email, f.phone as customer_phone, f.custom_attributes, f.is_duplicated, f.ip as customer_ip, f.location as customer_location, f.page_url, f.created_at as customer_created_at
        from accounts as a
        INNER JOIN campaigns cam On cam.account_id = a.id
        INNER JOIN channels ch On ch.campaign_id = cam.id
        INNER JOIN forms f On f.channel_id = ch.id
        where a.id = '".$account_id."' and f.created_at between '".$start_date."' and '".$end_date."' ");

        return $result;
    }
    
    public function campaignCall($campaign_id, $start_date, $end_date)
    {
        $result = DB::select("select a.id as account_id, a.company_name, a.adwords_account_id, a.created_at as account_created_at, a.updated_at as account_updated_at, a.facebook_account_id, a.currency, a.status as account_status, 
        cam.name as campaign_name, cam.status as campaign_status, cam.created_at as campaign_created_at, cam.updated_at as campaign_updated_at,
        ch.adwords_campaign_id, ch.kind, ch.status as channel_status, ch.url, ch.tracking_phone, ch.forward_phone, ch.facebook_campaign_id, ch.name as channel_name, ch.daily_net_budget, ch.daily_gross_budget, ch.daily_min_leads, ch.daily_max_leads, ch.created_at as channel_created_at, ch.updated_at as channel_updated_at,
        ca.date as call_date, ca.duration, ca.recording_url, ca.status as call_status, ca.phone, ca.is_duplicated, ca.location, ca.client_number, ca.call_uuid, ca.created_at as call_created_at
        from accounts as a
        INNER JOIN campaigns cam On cam.account_id = a.id
        INNER JOIN channels ch On ch.campaign_id = cam.id
        INNER JOIN calls ca On ca.channel_id = ch.id
        where cam.id = '".$campaign_id."' and ca.date between '".$start_date."' and '".$end_date."' ");

        return $result;
    }
    
    public function campaignForm($campaign_id, $start_date, $end_date)
    {
        $result = DB::select("select a.id as account_id, a.company_name, a.adwords_account_id, a.created_at as account_created_at, a.updated_at as account_updated_at, a.facebook_account_id, a.currency, a.status as account_status, 
        cam.name as campaign_name, cam.status as campaign_status, cam.created_at as campaign_created_at, cam.updated_at as campaign_updated_at,
        ch.adwords_campaign_id, ch.kind, ch.status as channel_status, ch.url, ch.tracking_phone, ch.forward_phone, ch.facebook_campaign_id, ch.name as channel_name, ch.daily_net_budget, ch.daily_gross_budget, ch.daily_min_leads, ch.daily_max_leads, ch.created_at as channel_created_at, ch.updated_at as channel_updated_at,
        f.name as customer_name, f.email as customer_email, f.phone as customer_phone, f.custom_attributes, f.is_duplicated, f.ip as customer_ip, f.location as customer_location, f.page_url, f.created_at as customer_created_at
        from accounts as a
        INNER JOIN campaigns cam On cam.account_id = a.id
        INNER JOIN channels ch On ch.campaign_id = cam.id
        INNER JOIN forms f On f.channel_id = ch.id
        where cam.id = '".$campaign_id."' and f.created_at between '".$start_date."' and '".$end_date."' ");

        return $result;
    }
}
