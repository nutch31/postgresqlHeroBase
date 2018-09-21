<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Call;
use App\Form;
use App\Config;
use App\Log_crontab;

class LeadService extends Controller
{
    //
    public function check_null($data)
    {
        if(is_null($data))
        {
            $data = '';
        }

        return $data;
    }

    public function leadServiceAccount()
    {
        set_time_limit(3600);
        
        $log_crontab = new Log_Crontab;
        $log_crontab->type = "Account";
        $log_crontab->skip = 0;
        $log_crontab->take = 0;
        $log_crontab->status = 0;
        $log_crontab->remark = "";
        $log_crontab->save();
      
        $accounts = DB::table("accounts")                
                ->orderBy('accounts.id', 'asc')
                ->get();

        $running = 0; 
        $remark = "";

        foreach($accounts as $account)
        {            
            $ch = curl_init("http://128.199.186.53/leadService/public/index.php/postaccount");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, true);

            $account->company_name       = $this->check_null($account->company_name);
            $account->company_street     = $this->check_null($account->company_street);
            $account->company_number     = $this->check_null($account->company_number);
            $account->company_compliment = $this->check_null($account->company_compliment);
            $account->company_city       = $this->check_null($account->company_city);
            $account->company_zip_code   = $this->check_null($account->company_zip_code);
            $account->company_state      = $this->check_null($account->company_state);
            $account->company_country    = $this->check_null($account->company_country);
            $account->adwords_account_id = $this->check_null($account->adwords_account_id);
            $account->advanced_analytics = $this->check_null($account->advanced_analytics);
            $account->created_at         = $this->check_null($account->created_at);
            $account->updated_at         = $this->check_null($account->updated_at);
            $account->skin               = $this->check_null($account->skin);
            $account->logo_id            = $this->check_null($account->logo_id);
            $account->facebook_account_id = $this->check_null($account->facebook_account_id);
            $account->contract_end_date  = $this->check_null($account->contract_end_date);
            $account->currency           = $this->check_null($account->currency);
            $account->active_on_sunday   = $this->check_null($account->active_on_sunday);
            $account->active_on_monday   = $this->check_null($account->active_on_monday);
            $account->active_on_tuesday  = $this->check_null($account->active_on_tuesday);
            $account->active_on_wednesday = $this->check_null($account->active_on_wednesday);
            $account->active_on_thursday = $this->check_null($account->active_on_thursday);
            $account->active_on_friday   = $this->check_null($account->active_on_friday);
            $account->active_on_saturday = $this->check_null($account->active_on_saturday);
            $account->status             = $this->check_null($account->status);
            $account->analytics          = $this->check_null($account->analytics);
                    
            $data = array(
                        'account_id' => $account->id,             
                        'company_name' => $account->company_name,         
                        'company_street' => $account->company_street,
                        'company_number' => $account->company_number,
                        'company_compliment' => $account->company_compliment,
                        'company_city' => $account->company_city,
                        'company_zip_code' => $account->company_zip_code,
                        'company_state' => $account->company_state,
                        'company_country' => $account->company_country,
                        'adwords_account_id' => $account->adwords_account_id,
                        'advanced_analytics' => $account->advanced_analytics,
                        'created_at_accounts' => $account->created_at,
                        'updated_at_accounts' => $account->updated_at,
                        'skin' => $account->skin,
                        'logo_id' => $account->logo_id,
                        'facebook_account_id' => $account->facebook_account_id,
                        'contract_end_date' => $account->contract_end_date,
                        'currency' => $account->currency,
                        'active_on_sunday' => $account->active_on_sunday,
                        'active_on_monday' => $account->active_on_monday,
                        'active_on_tuesday' => $account->active_on_tuesday,
                        'active_on_wednesday' => $account->active_on_wednesday,
                        'active_on_thursday' => $account->active_on_thursday,
                        'active_on_friday' => $account->active_on_friday,
                        'active_on_saturday' => $account->active_on_saturday,
                        'status' => $account->status,
                        'analytics' => $account->analytics
                    );
                    
            $data = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);  

            curl_close($ch);  

            if($info!=200 && $info!=201)
            {
                $remark .= $account->id.","; 
            }

            $running++;
        }
        
        $log_crontab = Log_Crontab::find($log_crontab->id);
        $log_crontab->take = $running;
        $log_crontab->status = 1;
        $log_crontab->remark = substr($remark,0,-1);
        $log_crontab->save();
    }
    
    public function leadServiceCampaign()
    {
        set_time_limit(3600);
        
        $log_crontab = new Log_Crontab;
        $log_crontab->type = "Campaign";
        $log_crontab->skip = 0;
        $log_crontab->take = 0;
        $log_crontab->status = 0;
        $log_crontab->remark = "";
        $log_crontab->save();
      
        $campaigns = DB::table("campaigns")
                ->orderBy('campaigns.id', 'asc')
                ->get();

        $running = 0; 
        $remark = "";

        foreach($campaigns as $campaign)
        {            
            $ch = curl_init("http://128.199.186.53/leadService/public/index.php/postcampaign");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, true);

            $campaign->name         = $this->check_null($campaign->name);
            $campaign->status       = $this->check_null($campaign->status);
            $campaign->start_date   = $this->check_null($campaign->start_date);
            $campaign->end_date     = $this->check_null($campaign->end_date);
            $campaign->account_id   = $this->check_null($campaign->account_id);
            $campaign->created_at   = $this->check_null($campaign->created_at);
            $campaign->updated_at   = $this->check_null($campaign->updated_at);
                    
            $data = array(
                        'campaign_id' => $campaign->id,             
                        'name' => $campaign->name,         
                        'status' => $campaign->status,
                        'start_date' => $campaign->start_date,
                        'end_date' => $campaign->end_date,
                        'account_id' => $campaign->account_id,
                        'created_at_campaigns' => $campaign->created_at,
                        'updated_at_campaigns' => $campaign->updated_at
                    );
                    
            $data = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);  

            curl_close($ch);  

            if($info!=200 && $info!=201)
            {
                $remark .= $campaign->id.","; 
            }

            $running++;
        }
        
        $log_crontab = Log_Crontab::find($log_crontab->id);
        $log_crontab->take = $running;
        $log_crontab->status = 1;
        $log_crontab->remark = substr($remark,0,-1);
        $log_crontab->save();
    }
    
    public function leadServiceChannel()
    {
        set_time_limit(7200);
        
        $log_crontab = new Log_Crontab;
        $log_crontab->type = "Channel";
        $log_crontab->skip = 0;
        $log_crontab->take = 0;
        $log_crontab->status = 0;
        $log_crontab->remark = "";
        $log_crontab->save();
      
        $channels = DB::table("channels")
                ->orderBy('channels.id', 'asc')
                ->get();

        $running = 0; 
        $remark = "";

        foreach($channels as $channel)
        {            
            $ch = curl_init("http://128.199.186.53/leadService/public/index.php/postchannel");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, true);
            
            $channel->campaign_id           = $this->check_null($channel->campaign_id);
            $channel->adwords_campaign_id   = $this->check_null($channel->adwords_campaign_id);
            $channel->kind                  = $this->check_null($channel->kind);
            $channel->status                = $this->check_null($channel->status);
            $channel->url                   = $this->check_null($channel->url);
            $channel->tracking_phone        = $this->check_null($channel->tracking_phone);
            $channel->forward_phone         = $this->check_null($channel->forward_phone);
            $channel->created_at            = $this->check_null($channel->created_at);
            $channel->updated_at            = $this->check_null($channel->updated_at);
            $channel->facebook_campaign_id  = $this->check_null($channel->facebook_campaign_id);
            $channel->name                  = $this->check_null($channel->name);
            $channel->daily_net_budget      = $this->check_null($channel->daily_net_budget);
            $channel->daily_gross_budget    = $this->check_null($channel->daily_gross_budget);
            $channel->daily_min_leads       = $this->check_null($channel->daily_min_leads);
            $channel->daily_max_leads       = $this->check_null($channel->daily_max_leads);
                    
            $data = array(
                        'channel_id' => $channel->id,             
                        'campaign_id' => $channel->campaign_id,         
                        'adwords_campaign_id' => $channel->adwords_campaign_id,
                        'kind' => $channel->kind,
                        'status' => $channel->status,
                        'url' => $channel->url,
                        'tracking_phone' => $channel->tracking_phone,
                        'forward_phone' => $channel->forward_phone,
                        'created_at_channels' => $channel->created_at,
                        'updated_at_channels' => $channel->updated_at,
                        'facebook_campaign_id' => $channel->facebook_campaign_id,
                        'name' => $channel->name,
                        'daily_net_budget' => $channel->daily_net_budget,
                        'daily_gross_budget' => $channel->daily_gross_budget,
                        'daily_min_leads' => $channel->daily_min_leads,
                        'daily_max_leads' => $channel->daily_max_leads
                    );
                    
            $data = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);  

            curl_close($ch);  

            if($info!=201 && $info!=202)
            {
                $remark .= $channel->id.","; 
            }

            $running++;
        }
        
        $log_crontab = Log_Crontab::find($log_crontab->id);
        $log_crontab->take = $running;
        $log_crontab->status = 1;
        $log_crontab->remark = substr($remark,0,-1);
        $log_crontab->save();
    }

    public function leadServiceCall()
    {
        set_time_limit(300);

        $config = Config::where('type', '=', 'leadServiceCall')->first();

        $log_crontab = new Log_Crontab;
        $log_crontab->type = $config->type;
        $log_crontab->skip = $config->skip;
        $log_crontab->take = 0;
        $log_crontab->status = 0;
        $log_crontab->remark = "";
        $log_crontab->save();
      
        $calls = DB::table("calls")
                ->orderBy('calls.id', 'asc')
                ->skip($config->skip)
                ->take($config->take)
                ->get();
                    
        $running = 0; 
        $remark = "";

        foreach($calls as $call)
        {            
            $ch = curl_init("http://128.199.186.53/leadService/public/index.php/postcall");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, true);
            
            $call->date             = $this->check_null($call->date);
            $call->duration         = $this->check_null($call->duration);
            $call->recording_url    = $this->check_null($call->recording_url);
            $call->status           = $this->check_null($call->status);
            $call->phone            = $this->check_null($call->phone);
            $call->channel_id       = $this->check_null($call->channel_id);
            $call->is_duplicated    = $this->check_null($call->is_duplicated);
            $call->location         = $this->check_null($call->location);
            $call->created_at       = $this->check_null($call->created_at);
            $call->updated_at       = $this->check_null($call->updated_at);
            $call->client_number    = $this->check_null($call->client_number);
            $call->call_uuid        = $this->check_null($call->call_uuid);
            $call->call_mapped      = $this->check_null($call->call_mapped);
                    
            $data = array(
                        'call_id' => $call->id,             
                        'date' => $call->date,         
                        'duration' => $call->duration,
                        'recording_url' => $call->recording_url,
                        'status' => $call->status,
                        'phone' => $call->phone,
                        'channel_id' => $call->channel_id,
                        'is_duplicated' => $call->is_duplicated,
                        'location' => $call->location,
                        'created_at' => $call->created_at,
                        'updated_at' => $call->updated_at,
                        'client_number' => $call->client_number,
                        'call_uuid' => $call->call_uuid,
                        'call_mapped' => $call->call_mapped
                    );
                    
            $data = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);  

            curl_close($ch);  

            if($info!=201)
            {
                $remark .= $call->id.","; 
            }

            $running++;
        }
        
        $log_crontab = Log_Crontab::find($log_crontab->id);
        $log_crontab->take = $running;
        $log_crontab->status = 1;
        $log_crontab->remark = substr($remark,0,-1);
        $log_crontab->save();

        $config = Config::find($config->id);
        $config->skip = $config->skip + $running;
        $config->save();
    }

    public function leadServiceForm()
    {
        set_time_limit(300);

        $config = Config::where('type', '=', 'leadServiceForm')->first();

        $log_crontab = new Log_Crontab;
        $log_crontab->type = $config->type;
        $log_crontab->skip = $config->skip;
        $log_crontab->take = 0;
        $log_crontab->status = 0;
        $log_crontab->remark = "";
        $log_crontab->save();
        
        $forms = DB::table("forms")
                ->orderBy('forms.id', 'asc')
                ->skip($config->skip)
                ->take($config->take)
                ->get();
                    
        $running = 0; 
        $remark = "";
                
        foreach($forms as $form)
        {                                        
            $ch = curl_init("http://128.199.186.53/leadService/public/index.php/postform");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, true);
            
            $form->channel_id       = $this->check_null($form->channel_id);
            $form->name             = $this->check_null($form->name);
            $form->email            = $this->check_null($form->email);
            $form->phone            = $this->check_null($form->phone);
            $form->custom_attributes  = $this->check_null($form->custom_attributes);
            $form->is_duplicated      = $this->check_null($form->is_duplicated);
            $form->ip               = $this->check_null($form->ip);
            $form->location         = $this->check_null($form->location);
            $form->created_at       = $this->check_null($form->created_at);
            $form->updated_at       = $this->check_null($form->updated_at);
            $form->page_url         = $this->check_null($form->page_url);
                
            $data = array(
                        'form_id' => $form->id,
                        'channel_id' =>  $form->channel_id,
                        'name' =>  $form->name,
                        'email' => $form->email,
                        'phone' => $form->phone,
                        'custom_attributes' => $form->custom_attributes,  
                        'is_duplicated' => $form->is_duplicated,
                        'ip' => $form->ip,
                        'location' => $form->location,
                        'created_at' => $form->created_at,
                        'updated_at' => $form->updated_at,             
                        'page_url' => $form->page_url 
                    );
                
            $data = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            $info = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
        
            curl_close($ch);  

            if($info!=201)
            {
                $remark .= $form->id.","; 
            }

            $running++;
        }
        
        $log_crontab = Log_Crontab::find($log_crontab->id);
        $log_crontab->take = $running;
        $log_crontab->status = 1;
        $log_crontab->remark = substr($remark,0,-1);
        $log_crontab->save();

        $config = Config::find($config->id);
        $config->skip = $config->skip + $running;
        $config->save();        
    }
}
