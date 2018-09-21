<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Account;
use App\Herobase;
//use DB;

class ConnectPostgreSQL extends Controller
{
    //
    public function Call(Request $request)
    {
        $herobase = new Herobase();
        $response = $herobase->Call($request["account_id"], $request["channel_id"], $request["limit"], $request["offset"]);
        
        return response()->json($response, '200');
    }

    public function Form(Request $request)
    {
        $herobase = new Herobase();
        $response = $herobase->Form($request["account_id"], $request["channel_id"], $request["limit"], $request["offset"]);
        
        return response()->json($response, '200');
    }

    public function Crontab_call(Request $request)
    {
        $herobase = new Herobase();
        $response = $herobase->Crontab_call();
        
        return response()->json($response, '200');
    }

    public function Crontab_form(Request $request)
    {
        $herobase = new Herobase();
        $response = $herobase->Crontab_form();
        
        return response()->json($response, '200');
    }

    public function SearchHeroBase(Request $request)
    {
        $herobase = new Herobase();
        $response = $herobase->SearchHeroBase(
            $request["adwords_account_id"], $request["facebook_account_id"], 
            $request["adwords_campaign_id"], $request["facebook_campaign_id"], 
            $request["callfrom_start_date"], $request["callfrom_end_date"]
        );
        
        return response()->json($response, '200');
    }

    public function accountCall(Request $request)
    {
        
        $herobase = new Herobase();
        $response = $herobase->accountCall($request["account_id"], $request["start_date"], $request["end_date"]);

        return response()->json($response, '200');
    }
    
    public function accountForm(Request $request)
    {
        
        $herobase = new Herobase();
        $response = $herobase->accountForm($request["account_id"], $request["start_date"], $request["end_date"]);

        return response()->json($response, '200');
    }
    
    public function campaignCall(Request $request)
    {
        
        $herobase = new Herobase();
        $response = $herobase->campaignCall($request["campaign_id"], $request["start_date"], $request["end_date"]);

        return response()->json($response, '200');
    }
    
    public function campaignForm(Request $request)
    {
        
        $herobase = new Herobase();
        $response = $herobase->campaignForm($request["campaign_id"], $request["start_date"], $request["end_date"]);

        return response()->json($response, '200');
    }










    public function index()
    {
        $query = new Herobase();
        //$account_id = 1;
        $campaign_id = 2;
        //$result = $query->account_call($account_id);
        //$result = $query->account_form($account_id);
        //$result = $query->campaign_call($campaign_id);
        $result = $query->campaign_form($campaign_id);
        dd($result);



        //$user = new User;

        //$user->name = 'Nut Chantathabxe';
        //$user->email = 'nut@heroleadsxe.com';
        //$user->password = 'password3xe';

        //$user->save();

        //$account = Account::find(167);
        //echo $account->company_name;
       // dd($account);

        //if(DB::connection()->getDatabaseName())
        //{
        //    echo "connected successfully to database ".DB::connection()->getDatabaseName();
        //}

        //return 'Hello Index';
    }

    public function test()
    {
        $array1 = array(
            'TEST' => 'AAAAAAAAAAAAA',
            'TEST2' => 'BBBBBBBBBBBBBBBBBB'
        );

        $array2 = array(
            'call' => array(
                array(
                'TEST3' => 'SYSTEM',
                'TEST4' => 'SYSTEM2',
                'TEST5' => 'XXXX'
                ),
                array(
                    'TEST3' => 'SYSTEM',
                    'TEST4' => 'SYSTEM2',
                    'TEST5' => 'XXXX'
                )
            )
        );

        $array3 = array(
            'form' => array(
                array(
                'TEST3' => 'SYSTEM',
                'TEST4' => 'SYSTEM2',
                'TEST5' => 'XXXX'
                ),
                array(
                    'TEST3' => 'SYSTEM',
                    'TEST4' => 'SYSTEM2',
                    'TEST5' => 'XXXX'
                )
            )
        );

        $array = array_merge($array1, $array2);
        $array = array_merge($array, $array3);

        return response()->json($array, '200');
    }
}
