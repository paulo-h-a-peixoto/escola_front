<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Psidisableday;
use App\Models\Reservation;

use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    public function getDisabledDates($id) {
        $array = ['error' => '', 'list' => []];

        $psicologo = User::select()
            ->where('id', $id)
            ->where('tipoUsuario', '3')
            ->first();
        if($psicologo){

            $disabledDays = Psidisableday::where('id_psicologo', $id)->get();

            foreach($disabledDays as $disabledDay){
                $array['list'][] = $disabledDay['day'];
            }

            $allowedDays = explode(',', $psicologo['dias']);
            $offDays = [];
            for($q=0; $q<7; $q++){
                if(!in_array($q, $allowedDays)){
                    $offDays[] = $q;
                }
            }

            $start = time();
            $end = strtotime('+3 months');
            $current = $start;
            $keep = true;
            while($keep){
                if($current < $end) {

                    $wd = date('w', $current);
                    if(in_array($wd, $offDays)){
                        $array['list'][] = date('Y-m-d', $current);
                    }

                    $current = strtotime('+1 day', $current);

                }else {
                    $keep = false;
                }
            }


            


        }else{
            $array['error'] = 'Psicologo inexistente';
            return $array;
        }
        
        

        

        return $array;
    }

    public function setReservation($id, Request $request) {

        


        $array = ['error' => '', 'list'=>[]];
        
        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i:s'
        ]);

        if(!$validator->fails()) {
            $date = $request->input('date');
            $time = $request->input('time');
            $user = auth()->user();
            $usuario = $user['id'];

            $psicologo = User::select()
            ->where('id', $id)
            ->where('tipoUsuario', '3')
            ->first();
            
            if($psicologo) {
                $can = true;
                $weekday = date('w', strtotime($date));
                $allowedDays = explode(',', $psicologo['dias']);
                if(!in_array($weekday, $allowedDays)){
                    $can = false;
                }else{
                    $start = strtotime($psicologo['start_time']);
                    $end = strtotime('-1 hour', strtotime($psicologo['end_time']));
                    $revtime = strtotime($time);
                    if($revtime < $start || $revtime > $end) {
                        $can = false;
                    }
                }

                $existingDisabledDay = Psidisableday::where('id_psicologo', $id)
                ->where('day', $date)
                ->count();
                if($existingDisabledDay > 0) {
                    $can = false;
                }

                $existingReservations = Reservation::where('id_psicologo', $id)
                ->where('reservation_date', $date.' '.$time)
                ->count();
                if($existingReservations > 0){
                    $can = false;
                }
                if($can){

                    // Gerar Boleto

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
                        "amount" => 35059,
                        "payment_method" => "boleto",
                        "customer" => [
                            
                            "external_id" => "0001",
                            "name" => $user['nome'],
                            "type" => "individual",
                            "country" => "br",
                            "email" => $user['email'],
                            "documents" => [
                                [
                                    "type" => "cpf",
                                    "number" => $user['cpf']
                                ]
                            ],
                        ]
                        
                    ];
                

                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

                    //for debug only!
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                    $resp = curl_exec($curl);
                    curl_close($curl);
                    $resp = json_decode($resp);

                    

                    $newReservation = new Reservation();
                    $newReservation->id_psicologo = $id;
                    $newReservation->id_usuario = $usuario;
                    $newReservation->reservation_date = $date.' '.$time;
                    $newReservation->cod_boleto = $resp->id;
                    $newReservation->save();

                    $array['list'][] = [
                        'boleto' => $resp
                    ];

                } else {
                    $array['error'] = 'Reserva não permitida neste dia/horário';
                    return $array;
                }
            }else{
                $array['error'] = 'Dados incorretos';
                return $array;
            }
        }else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function getTimes($id, Request $request) {
        $array = ['error' => '', 'list' => []];

        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d'
        ]);

        if(!$validator->fails()) {
            $date = $request->input('date');
            $psicologo = User::select()
            ->where('id', $id)
            ->where('tipoUsuario', '3')
            ->first();
            if($psicologo){

                $can = true;
                $existingDisabledDay = Psidisableday::where('id_psicologo', $id)
                    ->where('day', $date)
                    ->count();
                
                if($existingDisabledDay > 0){
                    $can = false;
                }

                $allowedDays = explode(',', $psicologo['dias']);
                $weekday = date('w', strtotime($date));

                if(!in_array($weekday, $allowedDays)) {
                    $can = false;
                }
                if($can) {
                    $start = strtotime($psicologo['start_time']);
                    $end = strtotime($psicologo['end_time']);
                    $times = [];
                    
                    for(
                        $lastTime = $start;
                        $lastTime < $end;
                        $lastTime = strtotime('+1 hour', $lastTime)
                    ) {
                        $times[] = $lastTime;
                    }

                    $timeList = [];

                    foreach($times as $time) {
                        $timeList[] = [
                            'id' => date('H:i:s', $time),
                            'title' => date('H:i', $time).' - '.date('H:i', strtotime('+1 hour', $time))
                        ];
                    }

                    $reservations = Reservation::where('id_psicologo', $id)
                        ->whereBetween('reservation_date', [
                            $date.' 00:00:00',
                            $date.' 23:59:59'
                        ])
                        ->get();

                    $toRemove = [];
                    foreach($reservations as $reservation) {
                        $time = date('H:i:s', strtotime($reservation['reservation_date']));
                        $toRemove[] = $time;
                    }

                    foreach($timeList as $timeItem) {
                        if(!in_array($timeItem['id'], $toRemove)) {
                            $array['list'][] = $timeItem;
                        }
                    }


                }
                
            }else{
                $array['error'] = 'Psicologo inexistente';
                return $array;
            }

        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }
        
        return $array;
    }

    public function getMyReservations(Request $request) {
        $array = ['error' => '', 'list' => []];
        $user = auth()->user();
        $usuario = $user['id'];
        if($usuario) {
            $user = User::find($usuario);
            if($user){

                $reservations = Reservation::where('id_usuario', $usuario)
                ->orderBy('reservation_date', 'DESC')
                ->get();

                foreach($reservations as $reservation){
                    $psicologo = User::select()
                    ->where('id', $reservation['id_psicologo'])
                    ->where('tipoUsuario', '3')
                    ->first();

                    $daterev = date('d/m/Y H:i', strtotime($reservation['reservation_date']));
                    $aftertime = date('H:i', strtotime('+1 hour', strtotime($reservation['reservation_date'])));
                
                    $daterev .= ' à '.$aftertime;

                    $array['list'][] = [
                        'id' => $reservation['id'],
                        'id_psicologo' => $reservation['id_psicologo'],
                        'status' => $reservation['pagamento'],
                        'title' => $psicologo['nome'],
                        'image' => asset('documentos/'.$psicologo['foto']),
                        'datereserved' => $daterev
                    ];
                }

            }else{
                $array['error'] = 'Usuário inexistente';
                return $array;
            }
        }else {
            $array['error'] = 'Usuário necessário';
            return $array;
        }
        return $array;
    }

    public function myClients(Request $request) {
        $array = ['error' => '', 'list' => []];
        $user = auth()->user();
        $usuario = $user['id'];
        if($usuario) {
            $user = User::find($usuario);
            if($user && $user['tipoUsuario'] == 3){

                $reservations = Reservation::where('id_psicologo', $usuario)
                ->where('status', 0)
                ->orderBy('reservation_date', 'DESC')
                ->get();

                foreach($reservations as $reservation){
                    $usuario = User::select()
                    ->where('id', $reservation['id_usuario'])
                    ->first();

                    $daterev = date('d/m/Y H:i', strtotime($reservation['reservation_date']));
                    $aftertime = date('H:i', strtotime('+1 hour', strtotime($reservation['reservation_date'])));
                
                    $daterev .= ' à '.$aftertime;

                    $array['list'][] = [
                        'id' => $reservation['id'],
                        'id_usuario' => $reservation['id_usuario'],
                        'title' => $usuario['nome'],
                        'image' => asset('documentos/'.$usuario['foto']),
                        'datereserved' => $daterev
                    ];
                }

            }else{
                $array['error'] = 'Você não é um Psicologo';
                return $array;
            }
        }else {
            $array['error'] = 'Psicologo necessário';
            return $array;
        }
        return $array;
    }

    public function myClientsFinanceiro(){
        $array = ['error' => '', 'list' => []];

        $clientes = User::select()
                    ->where('tipoUsuario', 1)
                    ->get();

        foreach ($clientes as $cliente) {
            $array['list'][] = [
                'id' => $cliente['id'],
                'nome' => $cliente['nome'],
                'image' => asset('documentos/'.$cliente['foto']),
                'nascimento' => $cliente['nascimento']
            ];
        }

        return $array;

    }

    public function getClienteInfo($id, Request $request){
        $array = ['error' => '', 'list' => []];

        $cliente = User::find($id);
        if($cliente){
        $array['list']['cliente'] = $cliente;

        $reservation = Reservation::where('id_usuario', $id)
                ->where('status', 2)
                ->orderBy('vencimento', 'DESC')
                ->get();

        foreach($reservation as $item){
            $daterev = date('d/m/Y H:i', strtotime($item['reservation_date']));
            $aftertime = date('H:i', strtotime('+1 hour', strtotime($item['reservation_date'])));
                
            $daterev .= ' à '.$aftertime;
            $array['list']['reservas'][] = [
                'id' => $item['id'],
                'psicologo' => User::find($item['id_psicologo']),
                'reservation_date' => $daterev,
                'vencimento' => $item['vencimento'],
                'complemento' => $item['complemento'],
                'status' => $item['status'],
                'qt_sessao' => $item['qt_sessao'],
                'valor' => $item['valor'],
                'duracao' => $item['duracao'],
                'pagamento' => $item['pagamento'],
                'dt_pagamento' => $item['dt_pagamento']
            ];    
        }


        }else{
            $array['error'] = 'Cliente inexistente';
            return $array;
        }

        return $array;
    }

    public function setPagamentoCliente($id, Request $request){
        $array = ['error' => ''];

        $user = auth()->user();
        
        $user = User::find($user['id']);
        if($user && $user['tipoUsuario'] == 2){
            $reservation = Reservation::find($id);
            $reservation->pagamento = $request->input('pagamento');
            $reservation->dt_pagamento = $request->input('dt_pagamento');
            $reservation->save();
        }else{
            $array['error'] = 'Você não é Gestor';
            return $array;
        }
        return $array;
    }

    public function finalizarClient(Request $request) {
        $array = ['error' => ''];
        
        $validator = Validator::make($request->all(), [
            'vencimento' => 'required',
            'qt_sessao' => 'required',
            'valor' => 'required',
            'duracao' => 'required'
        ]);
        
        if(!$validator->fails()){
            $novadata = str_replace("/", "-", $request->input('vencimento'));
            $reservation = Reservation::find($request->input('id'));
            $valor = substr($request->input('valor'), 2);
            if($reservation){
                $reservation->vencimento = date('Y-m-d', strtotime($novadata));
                $reservation->valor = number_format(str_replace(",",".",str_replace(".","",$valor)), 2, '.', '');
                $reservation->complemento = $request->input('complemento');
                $reservation->status = 2;
                $reservation->duracao = $request->input('duracao');
                $reservation->qt_sessao = $request->input('qt_sessao');
                $reservation->save();
            }else{
                $array['error'] = 'Paciente inexistente';
                return $array;
            }
        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }
        return $array;
    }

    

    public function delMyReservation($id) {
        $array = ['error' => ''];

        $user = auth()->user();
        $reservation = Reservation::find($id);
        if($reservation){

            if($reservation['id_usuario'] == $user['id']){
                Reservation::find($id)->delete();
            }else{
                $array['error'] = 'Esta reserva não é sua';
                return $array;
            }
        }else{
            $array['error'] = 'Reserva inexistente';
            return $array;
        }

        return $array;
    }
}


// $payload = array(
//     'to' => 'ExponentPushToken[oy8cc7Pv_U5bKPzJqZ9mCK]',
//     'sound' => 'default',
//     'title' => 'Paulo!',
//     'body' => 'Seu agendamento foi realizado com sucesso!',
// );

// $curlNotificacao = curl_init();

// curl_setopt_array($curlNotificacao, array(
// CURLOPT_URL => "https://exp.host/--/api/v2/push/send",
// CURLOPT_RETURNTRANSFER => true,
// CURLOPT_ENCODING => "",
// CURLOPT_MAXREDIRS => 10,
// CURLOPT_TIMEOUT => 30,
// CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
// CURLOPT_CUSTOMREQUEST => "POST",
// CURLOPT_POSTFIELDS => json_encode($payload),
// CURLOPT_HTTPHEADER => array(
//     "Accept: application/json",
//     "Accept-Encoding: gzip, deflate",
//     "Content-Type: application/json",
//     "cache-control: no-cache",
//     "host: exp.host"
// ),
// ));

// $response = curl_exec($curlNotificacao);
// $err = curl_error($curlNotificacao);

// curl_close($curlNotificacao);

// if ($err) {
// echo "cURL Error #:" . $err;
// } else {
// echo $response;
// }
// exit;