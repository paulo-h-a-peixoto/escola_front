<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use App\Models\Medico;
use App\Models\Medicoscomentario;

class MedicoController extends Controller
{
    public function getAll(){
        $array = ['error' => ''];
        
        $array['list'] = Medico::all();

        return $array;
    }

    public function getId(Request $request, $id) {
        $array = ['error' => ''];
        
        $medico = Medico::find($id);
        $array['list'] = $medico;    

        $comentarios = Medicoscomentario::select()
            ->where('ID_MEDICO', $id)
            ->get();
        $array['list']['comentarios'] = $comentarios;
        return $array;
    }

    public function insert(Request $request){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'NOME' => 'required',
            'ENDERECO' => 'required',
            'TELEFONE' => 'required',
            'CRM' => 'required',
            'DIA' => 'required',
            'HORA' => 'required',
            'ESPECIALIDADE' => 'required',
            'ID_PERIODO' => 'required'
        ]);

        if(!$validator->fails()){

            $newMedico = Medico::create($request->all());
        
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function removeId($id){
        $array = ['error' => ''];
           $medico = Medico::find($id);
           $medico->delete();
        return $array;
    }

    public function update(Request $request, $id){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'NOME' => 'required',
            'ENDERECO' => 'required',
            'TELEFONE' => 'required',
            'CRM' => 'required',
            'DIA' => 'required',
            'HORA' => 'required',
            'ID_PERIODO' => 'required',
            'ESPECIALIDADE' => 'required'
        ]);

        if(!$validator->fails()){

            $medico = Medico::find($id);

            $medico->NOME = $request->NOME;
            $medico->ENDERECO = $request->ENDERECO;
            $medico->TELEFONE = $request->TELEFONE;
            $medico->CRM = $request->CRM;
            $medico->DIA = $request->DIA;
            $medico->HORA = $request->HORA;
            $medico->ID_PERIODO = $request->ID_PERIODO;
            $medico->ESPECIALIDADE = $request->ESPECIALIDADE;

            $medico->save();
        
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

    public function getDisabledDates(){
        $array = ['error' => '', 'list' => []];
        
        $diasDisponiveis = Medico::select('DIA')->get();
        $inDays = array();
        foreach($diasDisponiveis as $dia){
            $inDays[] = $dia['DIA'];
        }

        $inicio = time();
        $final = strtotime('+3 months');
        $atual = $inicio;

        for(
           $atual = $inicio;
           $atual < $final;
           $atual = strtotime('+1 day', $atual)
        ){
            if(!in_array( date('Y-m-d', $atual), $inDays)){
                $array['list'][] = date('Y-m-d', $atual);
                
            }
        }
        for($q = 0; $q < count($array['list']); $q++){
            $array['list'][$q] .= "T00:00:00";
        }


        
        return $array;
    }

    public function getMedicosAgendamentos(Request $request){
        $array = ['error' => '', 'list' => []];

        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d'
        ]);

        if(!$validator->fails()){
           
            $medicos = Medico::select()
            ->where('DIA', $request->input('date'))
            ->get();

            if($medicos){

                $array['list'] = $medicos;

            } else {
                $array['error'] = 'Não há medicos agendados para esse dia';
                return $array;
            }

        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function insertComentario(Request $request){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'ID_MEDICO' => 'required',
            'BODY' => 'required'
        ]);

        if(!$validator->fails()){
           
            $comentario = $request->all();
            $comentario['DATACRIACAO'] = date('Y-m-d H:i:s');
            Medicoscomentario::create($comentario);

        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }


}
