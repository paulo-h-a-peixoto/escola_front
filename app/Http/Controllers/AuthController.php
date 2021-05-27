<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class AuthController extends Controller
{
    public function unauthorized(){
        return response()->json([
            'error' => 'Não autorizado'
        ], 401);
    }

    public function register(Request $request){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'nome' => 'required',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|digits:11|unique:users,cpf',
            'password' => 'required',
            'password_confirm' => 'required|same:password'
        ]);
        $dados = $request->all();
        
        if(!$validator->fails()){

            if ($request->hasFile('foto')) {
                
                $data = $request->input('foto');
                
                $file = $request->file('foto')->store('/public');
                $file = explode('public/', $file);
                
                $destination = base_path() . '/public/documentos';
                $request->file('foto')->move($destination, $file[1]);
                $dados['foto'] = $file[1];
                
            } else {
                if($request->input('tipoUsuario') == '3'){
                    $dados['foto'] = 'avatardemo.png';
                }else{
                    $dados['foto'] = 'default.png';
                }
            }
           

            $dados['password'] = password_hash($dados['password'], PASSWORD_DEFAULT);
            $newUser = User::create($dados);

            $token = Auth::attempt([
                'cpf' => $request->cpf,
                'password' => $request->password
            ]); 
           

            if(!$token){
                $array['error'] = 'Ocorreu um erro.';
                return $array;
            }

            $array['token'] = $token;
            $array['user'] = auth()->user();
        
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }
        
        return $array;
    }

    public function login(Request $request){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'cpf' => 'required|digits:11',
            'password' => 'required'
        ]);

        if(!$validator->fails()){

            $token = Auth::attempt([
                'cpf' => $request->cpf,
                'password' => $request->password
            ]); 
           

            if(!$token){
                $array['error'] = 'CPF E/ou Senha estão errados.';
                return $array;
            }

            $array['token'] = $token;
            $array['user'] = auth()->user();

        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function validateToken(){
        $array = ['error' => ''];
        
        $array['user'] = auth()->user();
        
        return $array;
    }

    public function logout(){
        $array = ['error' => ''];

        auth()->logout();

        return $array;
    }
}
