<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model
{
    use HasFactory;

    protected $table = 'ingresos';
    protected $fillable = [
        'codigoEstudiante', 
        'nombreEstudiante', 
        'idPrograma', 
        'fechaIngreso', 
        'horaIngreso', 
        'horaSalida', 
        'idResponsable', 
        'idSala',
        'created_at', 
        'updated_at'
    ];
   // aqui tengo que hacer un cambio
    // Relación con el modelo Programa
    public function programa()
    {
        return $this->belongsTo(Programa::class, 'idPrograma');
    }

    // Relación con el modelo Sala
    public function sala()
    {
        return $this->belongsTo(Sala::class, 'idSala');
    }

    // Relación con el modelo Responsable
    public function responsable()
    {
        return $this->belongsTo(Responsable::class, 'idResponsable');
    }
    
}
