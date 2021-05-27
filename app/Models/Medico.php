<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    use HasFactory;
    public $timestamps = false;

    public $fillable = [
        'NOME',
        'ENDERECO',
        'TELEFONE',
        'CRM',
        'ESPECIALIDADE',
        'DIA',
        'HORA',
        'ID_PERIODO'
    ];
}
