<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Humor extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $table = 'humor';
    public $fillable = [
        'humor',
        'descricao',
        'data_criacao',
        'id_usuario'
    ];
}
