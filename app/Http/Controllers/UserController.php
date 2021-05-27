<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
class UserController extends Controller
{
    public function getPsicologos(Request $request) {
        $array = ['error' => ''];
        $psicologos = User::select()
            ->where('tipoUsuario', '3')
            ->get();
        $array['list'] = $psicologos;
        return $array;
    }

    public function getClientes(){
        $array = ['error' => ''];
        $clientes = User::select()
            ->where('tipoUsuario', '1')
            ->get();
        foreach($clientes as $item){
            $array['list'][] = [
                'id' => $item['id'],
                'image' => asset('documentos/'.$item['foto']),
                'nome' => $item['nome']
            ];
        }
        return $array;
    }

    public function getPsicologosAgenda(Request $request){
        $array = ['error' => ''];
        $daysHelper = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'];

        $psicologos = User::select()
            ->where('tipoUsuario', '3')
            ->get();

        foreach($psicologos as $item){
            $dayList = explode(',', $item['dias']);

            $dayGroups = [];

            $lastDay = intval(current($dayList));
            $dayGroups[] = $daysHelper[$lastDay];
            array_shift($dayList);

            foreach($dayList as $day){
                if(intval($day) != $lastDay + 1){
                    $dayGroups[] = $daysHelper[$lastDay];
                    $dayGroups[] = $daysHelper[$day];
                }
                $lastDay = intval($day);
            }

            $dayGroups[] = $daysHelper[end($dayList)];
            $dates = '';
            $close = 0;
            foreach($dayGroups as $group) {
                if($close === 0){
                    $dates .= $group;
                }else{
                    $dates .= '-'.$group.',';
                }
                $close = 1 - $close;
            }

            $dates = explode(',', $dates);
            array_pop($dates);

            $start = date('H:i', strtotime($item['start_time']));
            $end = date('H:i', strtotime($item['end_time']));

            foreach($dates as $dkey => $dValue) {
                $dates[$dkey] .= ' '.$start.' às '.$end; 
            }

            $array['list'][] = [
                'id' => $item['id'],
                'image' => asset('documentos/'.$item['foto']),
                'nome' => $item['nome'],
                'dates' => $dates
            ];

        }

        return $array;
    }
}
