<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingreso;
use App\Models\HorarioSala;
use Carbon\Carbon;

class IngresoController extends Controller
{
    public function store(Request $request)
    {
        // Validación de los datos de ingreso
        $validated = $request->validate([
            'codigoEstudiante' => 'required|string|max:10',
            'nombreEstudiante' => 'required|string|max:250',
            'idPrograma' => 'required|exists:programas,id',
            'fechaIngreso' => 'required|date',
            'horaIngreso' => 'required|date_format:H:i',
            'idSala' => 'required|exists:salas,id',
            'idResponsable' => 'required|exists:responsables,id',
        ]);

        // Verificar disponibilidad de la sala en el horario seleccionado
        $dia = Carbon::parse($request->fechaIngreso)->format('l');
        $horariosOcupados = HorarioSala::where('idSala', $request->idSala)
            ->where('dia', $dia)
            ->where(function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('horaInicio', '<=', $request->horaIngreso)
                      ->where('horaFin', '>', $request->horaIngreso);
                })->orWhere(function ($q) use ($request) {
                    $q->where('horaInicio', '<', $request->horaIngreso)
                      ->where('horaFin', '>=', $request->horaIngreso);
                })->orWhere(function ($q) use ($request) {
                    $q->where('horaInicio', '>=', $request->horaIngreso)
                      ->where('horaFin', '<=', $request->horaIngreso);
                });
            })->exists();

        if ($horariosOcupados) {
            return response()->json([
                'error' => 'La sala no está disponible en el horario especificado.',
                'detalle' => [
                    'sala' => $request->idSala,
                    'dia' => $dia,
                    'horaIngreso' => $request->horaIngreso,
                ]
            ], 403);
        }

        // Registrar el ingreso
        $ingreso = Ingreso::create($validated);
        return response()->json($ingreso, 201);
    }

    // Registrar la salida de un estudiante
    public function updateSalida(Request $request, $id)
    {
        $validated = $request->validate([
            'horaSalida' => 'required|date_format:H:i',
        ]);

        $ingreso = Ingreso::findOrFail($id);

        // Validar que la salida sea posterior a la hora de ingreso
        if ($request->horaSalida <= $ingreso->horaIngreso) {
            return response()->json(['error' => 'La hora de salida debe ser posterior a la de ingreso'], 403);
        }

        // Validar que la salida esté en el horario permitido
        if (!$this->validarHorario($ingreso->fechaIngreso, $request->horaSalida)) {
            return response()->json(['error' => 'Hora de salida fuera del horario permitido'], 403);
        }

        $ingreso->update(['horaSalida' => $request->horaSalida]);
        return response()->json($ingreso);
    }

    // Mostrar ingresos del día actual
    public function ingresosDelDia()
    {
        $ingresos = Ingreso::whereDate('fechaIngreso', Carbon::today())->get();
        return response()->json($ingresos);
    }

    // Consultar ingresos 
    public function consultarIngresos(Request $request)
    {
        $query = Ingreso::query();

        if ($request->filled('fechaInicio') && $request->filled('fechaFin')) {
            $query->whereBetween('fechaIngreso', [$request->fechaInicio, $request->fechaFin]);
        }

        if ($request->filled('codigoEstudiante')) {
            $query->where('codigoEstudiante', $request->codigoEstudiante);
        }

        if ($request->filled('idPrograma')) {
            $query->where('idPrograma', $request->idPrograma);
        }

        if ($request->filled('idResponsable')) {
            $query->where('idResponsable', $request->idResponsable);
        }

        $ingresos = $query->get();
        return response()->json($ingresos);
    }
    // Actualizar datos de un estudiante
    public function updateIngreso(Request $request, $id)
    {
        $validated = $request->validate([
            'codigoEstudiante' => 'sometimes|string|max:10',
            'nombreEstudiante' => 'sometimes|string|max:250',
        ]);

        $ingreso = Ingreso::findOrFail($id);
        $ingreso->update(array_merge($validated, ['updated_at' => now()]));

        return response()->json($ingreso);
    }

    // Validación de horario permitido
    private function validarHorario($fecha, $hora)
    {
        $dia = Carbon::parse($fecha)->format('l');
        $horaPermitida = false;

        if (in_array($dia, ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'])) {
            $horaPermitida = ($hora >= '07:00' && $hora <= '20:50');
        } elseif ($dia == 'Saturday') {
            $horaPermitida = ($hora >= '07:00' && $hora <= '16:30');
        }

        return $horaPermitida;
    }
}
