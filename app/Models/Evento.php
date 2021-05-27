<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;
    public $timestamps = false;

    public $fillable = [
        'COMENTARIO',
        'DIA',
        'HORA',
        'TIPO_AVISO',
        'ARQUIVO'
    ];
}
