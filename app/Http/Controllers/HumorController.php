<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


use App\Models\Humor;

class HumorController extends Controller
{
    public function insert(Request $request){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'humor' => 'required'
        ]);

        if(!$validator->fails()){
        $user = auth()->user();
        $dados = $request->all();
        $dados['id_usuario'] = $user['id'];
        $dados['data_criacao'] = date('Y-m-d H:i:s');
        
        Humor::create($dados);

        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function getId($id, Request $request) {
        $array = ['error' => '', 'list' => []];
    
        //'image' => asset('documentos/'.$cliente['foto']),
        $humor = Humor::select()
            ->where('id_usuario', $id)
            ->get();

        if($humor){

            foreach($humor as $item){
                $title = '';

                if($item['humor'] == 1){
                    $title = 'Radiante';
                }else if($item['humor'] == 2){
                    $title = 'Bem';
                }else if($item['humor'] == 3){
                    $title = 'Mais ou menos';
                }else if($item['humor'] == 4){
                    $title = 'Mal';
                }else if($item['humor'] == 5){
                    $title = 'Horrível';
                }

                $array['list'][] = [
                    'time' => date('d/m/Y H:i', strtotime($item['data_criacao'])),
                    'title' => $title,
                    'description' => $item['descricao']
                ];

            }





        }else{
            $array['error'] = 'Este usuário ainda não tem humor cadastrado!';
            return $array;
        }

        return $array;
    }

    public function myHumor(Request $request) {
        $array = ['error' => '', 'list' => []];
    
        $user = auth()->user();
        $humor = Humor::select()
            ->where('id_usuario', $user['id'])
            ->get();

        if($humor){

            foreach($humor as $item){
                $title = '';

                if($item['humor'] == 1){
                    $title = 'Radiante';
                }else if($item['humor'] == 2){
                    $title = 'Bem';
                }else if($item['humor'] == 3){
                    $title = 'Mais ou menos';
                }else if($item['humor'] == 4){
                    $title = 'Mal';
                }else if($item['humor'] == 5){
                    $title = 'Horrível';
                }

                $array['list'][] = [
                    'id' => $item['id'],
                    'time' => date('d/m/Y H:i', strtotime($item['data_criacao'])),
                    'title' => $title,
                    'description' => $item['descricao']
                ];

            }





        }else{
            $array['error'] = 'Este usuário ainda não tem humor cadastrado!';
            return $array;
        }

        return $array;
    }

    public function delMyHumor($id, Request $request) {

        $array = ['error' => ''];
    
        $user = auth()->user();
        $humor = Humor::find($id);
        if($humor){

            if($humor['id_usuario'] == $user['id']){
                Humor::find($id)->delete();
            }else{
                $array['error'] = 'Este Humor não é seu!';
                return $array;
            }
        }else{
            $array['error'] = 'Humor inexistente';
            return $array;
        }

        return $array;

    }
}
