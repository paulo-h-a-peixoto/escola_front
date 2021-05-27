<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    public $timestamps = false;

    protected $hidden = [
        'password'
    ];

    public $fillable = [
        'nome',
        'cpf',
        'email',
        'password',
        'cep',
        'endereco',
        'tipoUsuario',
        'foto',
        'crp',
        'telefone',
        'nascimento',
        'psicologo',
        'abordagem',
        'dias',
        'time'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
