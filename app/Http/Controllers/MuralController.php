<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Muralaviso;

class MuralController extends Controller
{
    public function getAll(){
        $array = ['error' => ''];
        
        $mural = Muralaviso::all();

        $array['list'] = $mural;
        
        return $array;
    }
}
