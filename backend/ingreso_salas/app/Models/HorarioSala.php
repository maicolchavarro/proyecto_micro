<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HorarioSala extends Model
{
    use HasFactory;

    protected $table = 'horarios_salas';
    public $timestamps = false; 
    protected $fillable = [
        'idSala',
        'dia',
        'materia',
        'horaInicio',
        'horaFin',
        'idPrograma',
    ];

    public function sala()
    {
        return $this->belongsTo(Sala::class, 'idSala');
    }
    
    public function programa()
    {
        return $this->belongsTo(Programa::class, 'idPrograma');
    }
    
}
