<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HorarioSala;

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

        // Verificar solapamientos con horarios existentes
        $conflicto = HorarioSala::where('idSala', $validated['idSala'])
            ->where('dia', $validated['dia'])
            ->where(function ($query) use ($validated) {
                $query->where(function ($q) use ($validated) {
                    $q->where('horaInicio', '<=', $validated['horaInicio'])
                      ->where('horaFin', '>', $validated['horaInicio']);
                })->orWhere(function ($q) use ($validated) {
                    $q->where('horaInicio', '<', $validated['horaFin'])
                      ->where('horaFin', '>=', $validated['horaFin']);
                })->orWhere(function ($q) use ($validated) {
                    $q->where('horaInicio', '>=', $validated['horaInicio'])
                      ->where('horaFin', '<=', $validated['horaFin']);
                });
            })->exists();

        if ($conflicto) {
            return response()->json(['error' => 'El horario se solapa con otro existente'], 422);
        }

        // Crear el nuevo horario
        $horario = HorarioSala::create($validated);
        return response()->json($horario, 201);
    }

    // Actualizar el horario de una sala
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'horaInicio' => 'required|date_format:H:i',
            'horaFin' => 'required|date_format:H:i|after:horaInicio',
        ]);

        $horario = HorarioSala::find($id);
        if (!$horario) {
            return response()->json(['error' => 'Horario no encontrado'], 404);
        }

        // Validar que no haya solapamiento con otros horarios
        $conflicto = HorarioSala::where('idSala', $horario->idSala)
            ->where('dia', $horario->dia)
            ->where('id', '!=', $id)
            ->where(function ($query) use ($validated) {
                $query->where(function ($q) use ($validated) {
                    $q->where('horaInicio', '<=', $validated['horaInicio'])
                      ->where('horaFin', '>', $validated['horaInicio']);
                })->orWhere(function ($q) use ($validated) {
                    $q->where('horaInicio', '<', $validated['horaFin'])
                      ->where('horaFin', '>=', $validated['horaFin']);
                })->orWhere(function ($q) use ($validated) {
                    $q->where('horaInicio', '>=', $validated['horaInicio'])
                      ->where('horaFin', '<=', $validated['horaFin']);
                });
            })->exists();

        if ($conflicto) {
            return response()->json(['error' => 'El horario se solapa con otro existente'], 422);
        }

        $horario->update($validated);
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
