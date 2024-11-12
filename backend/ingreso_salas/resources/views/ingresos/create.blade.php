<form method="POST" action="{{ route('ingresos.store') }}">
    @csrf
    <label for="codigoEstudiante">Código de Estudiante:</label>
    <input type="text" name="codigoEstudiante" required>

    <label for="nombreEstudiante">Nombre del Estudiante:</label>
    <input type="text" name="nombreEstudiante" required>

    <label for="idPrograma">Programa:</label>
    <select name="idPrograma" required>
        @foreach($programas as $programa)
            <option value="{{ $programa->id }}">{{ $programa->nombre }}</option>
        @endforeach
    </select>

    <label for="fechaIngreso">Fecha de Ingreso:</label>
    <input type="date" name="fechaIngreso" required>

    <label for="horaIngreso">Hora de Ingreso:</label>
    <input type="time" name="horaIngreso" required>

    <label for="idSala">Sala:</label>
    <select name="idSala" required>
        @foreach($salas as $sala)
            <option value="{{ $sala->id }}">{{ $sala->nombre }}</option>
        @endforeach
    </select>

    <label for="idResponsable">Responsable:</label>
    <select name="idResponsable" required>
        @foreach($responsables as $responsable)
            <option value="{{ $responsable->id }}">{{ $responsable->nombre }}</option>
        @endforeach
    </select>

    <button type="submit">Registrar Ingreso</button>
</form>
