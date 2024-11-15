<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HorarioSala;
use Illuminate\Http\Response;

class HorarioSalaController extends Controller
{
    // Registrar un nuevo horario para una sala
    public function store(Request $request)
    {
        $validated = $request->validate([
            'idSala' => 'required|integer|exists:salas,id',
            'dia' => 'required|string|max:20',
            'materia' => 'required|string|max:100',
            'horaInicio' => 'required|date_format:H:i',
            'horaFin' => 'required|date_format:H:i|after:horaInicio',
            'idPrograma' => 'required|integer|exists:programas,id',
        ]);

    // Crear el nuevo horario de la sala
    
        $horario = HorarioSala::create($validated);
        return response()->json($horario, 201);
    }
    

        

    // Actualizar el horario de una sala
    public function update(Request $request, $id)
    {
        // Validar los datos de entrada
        $validated = $request->validate([
            'horaInicio' => 'required|date_format:H:i',
            'horaFin' => 'required|date_format:H:i|after:horaInicio',
        ]);
    
        // Buscar el horario por ID
        $horario = HorarioSala::find($id);
        if (!$horario) {
            return response()->json(['error' => 'Horario no encontrado'], 404);
        }
    
        // Validar que no haya solapamiento con otros horarios en la misma sala y día
        $conflicto = HorarioSala::where('idSala', $horario->idSala)
            ->where('dia', $horario->dia)
            ->where('id', '!=', $id)
            ->where(function ($query) use ($validated) {
                $query->whereBetween('horaInicio', [$validated['horaInicio'], $validated['horaFin']])
                      ->orWhereBetween('horaFin', [$validated['horaInicio'], $validated['horaFin']]);
            })->exists();
    
        if ($conflicto) {
            return response()->json(['error' => 'El horario se solapa con otro existente'], 422);
        }
    
        // Actualizar el horario
        $horario->horaInicio = $validated['horaInicio'];
        $horario->horaFin = $validated['horaFin'];
        $horario->save();
    
        // Retornar la respuesta exitosa
        return response()->json($horario, 200);
    }
    
    
    // Consultar los horarios de una sala en un día específico
    public function consultar(Request $request)
    {
        $validated = $request->validate([
            'idSala' => 'required|integer|exists:salas,id',
            'dia' => 'required|string',
        ]);
    
        $horarios = HorarioSala::where('idSala', $validated['idSala'])
            ->where('dia', $validated['dia'])
            ->get();
    
        return response()->json($horarios);
    }
    

    // Eliminar un horario de sala
    public function destroy($id)
    {
        $horario = HorarioSala::find($id);
        if (!$horario) {
            return response()->json(['error' => 'Horario no encontrado'], 404);
        }
    
        $horario->delete();
        return response()->json(['message' => 'Horario eliminado exitosamente']);
    }
    
}
