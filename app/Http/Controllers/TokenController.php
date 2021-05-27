<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Token;

class TokenController extends Controller
{
    public function insert(Request $request){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);

        if(!$validator->fails()) {
            $isToken = Token::select()
            ->where('token', $request->input('token'))
            ->first();
            if(!$isToken){
                Token::create($request->all());
            }
        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }
        
        return $array;
    }

    public function getAll(){
        $array = ['error' => '', 'list' => []];
        $array['list'] = Token::all();
        return $array;
    }

    public function boleto(Request $request) {
     

    $url = "https://api.pagar.me/1/transactions";

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $headers = array(
    "content-type: application/json",
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $data = 
    [
        "api_key" => "ak_live_tgdTZkHEPee6d9tf15CBxnw1TOGURA",
        "amount" => 35000,
        "payment_method" => "boleto",
        "customer" => [
            
            "external_id" => "0001",
            "name" => "Paulo Henrique Alves Peixoto",
            "type" => "individual",
            "country" => "br",
            "email" => "paulinho438@gmail.com",
            "documents" => [
                [
                    "type" => "cpf",
                    "number" => "05546356154"
                ]
            ],
            "phone_numbers" => ["+5561993305267"]
        ]
        
    ];
   

    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $resp = curl_exec($curl);
    curl_close($curl);
    $resp = json_decode($resp);
    print_r($resp->id);




    }

    public function boletoAutorizar(Request $request) {
        $url = "https://api.pagar.me/1/transactions/".$request->input('id')."?api_key=ak_test_Xyzo7aw9hry7kGTo41Y6r69WnQhKnY&status=paid";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_PUT, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "content-type: application/json",
            );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $data = [
            'status'=>'paid'
        ];
     
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        print_r($resp);
    }

    public function boletoConsulta(Request $request){
        $url = "https://api.pagar.me/1/transactions/".$request->input('id')."?api_key=".$request->input('api');

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
        "content-type: application/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        print_r($resp);
    }

}
