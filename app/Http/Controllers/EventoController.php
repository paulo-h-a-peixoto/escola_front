<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use App\Models\Evento;
use App\Models\Eventocomentario;

class EventoController extends Controller
{
    public function getAll(){
        $array = ['error' => ''];
        
        $array['list'] = Evento::all();

        return $array;
    }

    public function removeAll(){
        $array = ['error' => ''];
           Evento::truncate();
           
        return $array;
    }

    public function removeId($id){
        $array = ['error' => ''];
           $evento = Evento::find($id);
           $evento->delete();
        return $array;
    }

  

    public function getId(Request $request, $id) {
        $array = ['error' => ''];
        
        $medico = Evento::find($id);
        $array['list'] = $medico;    

        $comentarios = Eventocomentario::select()
            ->where('ID_EVENTO', $id)
            ->get();
        $array['list']['comentarios'] = $comentarios;
        return $array;
    }

    public function insert(Request $request){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'DIA' => 'required',
            'HORA' => 'required',
            'COMENTARIO' => 'required',
            'TIPO_AVISO' => 'required'
        ]);
        
        $rand = rand(0, 999);
        if(!$validator->fails()){
            $dados = $request->all();

            if ($request->hasFile('ARQUIVO')) {
                $data = $request->input('ARQUIVO');
                $arquivo = $rand.'-'.$request->file('ARQUIVO')->getClientOriginalName();
                $destination = base_path() . '/public/documentos';
                $request->file('ARQUIVO')->move($destination, $arquivo);
                $dados['ARQUIVO'] = $arquivo;
                
            }
           
            $newEvento = Evento::create($dados);
        
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function update(Request $request, $id){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'DIA' => 'required',
            'HORA' => 'required',
            'COMENTARIO' => 'required',
            'TIPO_AVISO' => 'required'
        ]);
        
        $rand = rand(0, 999);
        if(!$validator->fails()){
            $dados = $request->all();
            $evento = Evento::find($id);
            if ($request->hasFile('ARQUIVO')) {
                $data = $request->input('ARQUIVO');
                $arquivo = $rand.'-'.$request->file('ARQUIVO')->getClientOriginalName();
                $destination = base_path() . '/public/documentos';
                $request->file('ARQUIVO')->move($destination, $arquivo);
                $dados['ARQUIVO'] = $arquivo;
                $evento->ARQUIVO = $dados['ARQUIVO'];
                
            }
           
            
            $evento->COMENTARIO = $dados['COMENTARIO'];
            $evento->DIA = $dados['DIA'];
            $evento->HORA = $dados['HORA'];
            $evento->TIPO_AVISO = $dados['TIPO_AVISO'];

            $evento->save();
            
        
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    

    public function getAgendamento(Request $request){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d'
        ]);

        if(!$validator->fails()){
            $date = $request->input('date');


        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

   

    public function getEventoDia(Request $request){
        $array = ['error' => '', 'list' => []];

        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d'
        ]);

        if(!$validator->fails()){
           
            $eventos = Evento::select()
            ->where('DIA', $request->input('date'))
            ->get();

            if($eventos){

                $array['list'] = $eventos;

            } else {
                $array['error'] = 'Não há Eventos agendados para hoje.';
                return $array;
            }

        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    


}
