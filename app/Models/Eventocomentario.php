<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eventocomentario extends Model
{
    use HasFactory;
    public $timestamps = false;

    public $fillable = [
        'ID_EVENTO',
        'DATACRIACAO',
        'BODY'
    ];
}
