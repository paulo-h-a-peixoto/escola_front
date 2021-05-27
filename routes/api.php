<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\MuralController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\HumorController;
use App\Http\Controllers\TokenController;

Route::get('/ping', function(){
    return ['pong'=>true];
});

Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/registers', [AuthController::class, 'register']);


Route::post('/token', [TokenController::class, 'insert']);
Route::get('/token', [TokenController::class, 'getAll']);

Route::post('/boleto', [TokenController::class, 'boleto']);
Route::post('/boleto/autorizar', [TokenController::class, 'boletoAutorizar']);
Route::post('/boleto/consulta', [TokenController::class, 'boletoConsulta']);

Route::get('/psicologos', [UserController::class, 'getPsicologos']);


Route::middleware('auth:api')->group(function(){
    Route::post('/auth/validate', [AuthController::class, 'validateToken']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Psicologos

    Route::get('/psicologosagenda', [UserController::class, 'getPsicologosAgenda']);

    //Reservas
    Route::post('/reservation/{id}', [ReservationController::class, 'setReservation']);

    Route::get('/reservation/{id}/disableddates', [ReservationController::class, 'getDisabledDates']);
    Route::get('/reservation/{id}/times', [ReservationController::class, 'getTimes']);

    Route::get('/myreservations', [ReservationController::class, 'getMyReservations']);
    Route::get('/deletemyreservation/{id}', [ReservationController::class, 'delMyReservation']);

    Route::get('/myclientsfinanceiro', [ReservationController::class, 'myClientsFinanceiro']);

    Route::get('/clientinfo/{id}', [ReservationController::class, 'getClienteInfo']);
    Route::post('/clientpagamento/{id}', [ReservationController::class, 'setPagamentoCliente']);

    Route::post('/clienthumor', [HumorController::class, 'insert']);
    Route::get('/clienthumor/{id}', [HumorController::class, 'getId']);

    Route::get('/myhumor', [HumorController::class, 'myHumor']);
    Route::get('/deletemyhumor/{id}', [HumorController::class, 'delMyHumor']);

    Route::get('/myclients', [ReservationController::class, 'myClients']);
    Route::post('/myclient', [ReservationController::class, 'finalizarClient']);




    Route::get('/getclientes', [UserController::class, 'getClientes']);

    // Mural de Avisos
    Route::get('/mural', [MuralController::class, 'getAll']);

    // Medicos
    Route::get('/medicos', [MedicoController::class, 'getAll']);
    Route::get('/medico/{id}', [MedicoController::class, 'getId']);
    Route::post('/medico', [MedicoController::class, 'insert']);
    Route::post('/medico/edit/{id}', [MedicoController::class, 'update']);
    Route::post('/medico/comentario', [MedicoController::class, 'insertComentario']);
    Route::get('/medico/remove/{id}', [MedicoController::class, 'removeId']);

    
    // Medicos do dia

    Route::get('/agendamento', [MedicoController::class, 'getAgendamento']);
    Route::get('/getdisableddates', [MedicoController::class, 'getDisabledDates']);
    Route::get('/getmedicosagendamentos', [MedicoController::class, 'getMedicosAgendamentos']);

    // Evento
    Route::get('/eventos', [EventoController::class, 'getAll']);
    Route::get('/evento/{id}', [EventoController::class, 'getId']);
    Route::post('/evento', [EventoController::class, 'insert']);
    Route::post('/evento/edit/{id}', [EventoController::class, 'update']);
    Route::post('/evento/comentario', [EventoController::class, 'insertComentario']);
    
    // Eventos do dia

    Route::get('/eventos/agendamento', [EventoController::class, 'getAgendamento']);
    Route::get('/eventos/getdisableddates', [EventoController::class, 'getDisabledDates']);
    Route::get('/eventos/geteventodia', [EventoController::class, 'getEventoDia']);
    Route::get('/eventos/removeall', [EventoController::class, 'removeAll']);
    Route::get('/eventos/remove/{id}', [EventoController::class, 'removeId']);

});