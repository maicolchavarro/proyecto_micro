const API_URL = "http://localhost:8000/api";

async function apiRequest(endpoint, method = "GET", body = null) {
    const options = {
        method,
        headers: { "Content-Type": "application/json" },
        body: body ? JSON.stringify(body) : null,
    };
    const response = await fetch(`${API_URL}${endpoint}`, options);
    if (!response.ok) {
        alert(`Error: ${response.statusText}`);
        return null;
    }
    return await response.json();
}

async function cargarOpciones(endpoint, selectId) {
    const result = await apiRequest(endpoint, "GET");
    if (!result) return;

    const select = document.getElementById(selectId);
    select.innerHTML = '<option value="">Seleccionar</option>'; 
    result.forEach(item => {
        const option = document.createElement("option");
        option.value = item.id; 
        option.textContent = item.nombre || item.descripcion; 
        select.appendChild(option);
    });
}

// Registrar horario
document.getElementById("formRegistrarHorario").addEventListener("submit", async (e) => {
    e.preventDefault();
    const body = {
        idSala: document.getElementById("idSala").value,
        dia: document.getElementById("dia").value,
        materia: document.getElementById("materia").value,
        horaInicio: document.getElementById("horaInicio").value,
        horaFin: document.getElementById("horaFin").value,
        idPrograma: document.getElementById("idPrograma").value,
    };

    const result = await apiRequest("/horarios-salas", "POST", body);
    if (result) alert("Horario registrado exitosamente");
});
// Consultar horarios
document.getElementById("formConsultarHorarios").addEventListener("submit", async (e) => {
    e.preventDefault();
    const idSala = document.getElementById("idSalaConsulta").value;
    const dia = document.getElementById("diaConsulta").value;
    const result = await apiRequest(`/horarios-salas?idSala=${idSala}&dia=${dia}`);

    const container = document.getElementById("horariosResultado");
    container.innerHTML = "";

    if (result && result.length) {
        // Crear la tabla
        const table = document.createElement("table");
        const thead = document.createElement("thead");
        const tbody = document.createElement("tbody");

        const headers = ["ID", "Sala", "Día", "Materia", "Hora Inicio", "Hora Fin"];
        const headerRow = document.createElement("tr");
        headers.forEach(header => {
            const th = document.createElement("th");
            th.textContent = header;
            headerRow.appendChild(th);
        });
        thead.appendChild(headerRow);

        result.forEach(horario => {
            const row = document.createElement("tr");
            const fields = [horario.id, horario.idSala, horario.dia, horario.materia, horario.horaInicio, horario.horaFin];
            fields.forEach(field => {
                const td = document.createElement("td");
                td.textContent = field;
                row.appendChild(td);
            });
            tbody.appendChild(row);
        });

        table.appendChild(thead);
        table.appendChild(tbody);
        container.appendChild(table);
    } else {
        container.textContent = "No se encontraron horarios.";
    }
});

// Actualizar horario
document.getElementById("formActualizarHorario").addEventListener("submit", async (e) => {
    e.preventDefault();
    const idHorario = document.getElementById("idHorario").value;
    const body = {
        horaInicio: document.getElementById("horaInicioActualizar").value,
        horaFin: document.getElementById("horaFinActualizar").value,
    };

    const result = await apiRequest(`/horarios-salas/${idHorario}`, "PUT", body);
    if (result) alert("Horario actualizado exitosamente");
});

// Eliminar horario
document.getElementById("formEliminarHorario").addEventListener("submit", async (e) => {
    e.preventDefault();
    const idHorario = document.getElementById("idHorarioEliminar").value;
    const result = await apiRequest(`/horarios-salas/${idHorario}`, "DELETE");
    if (result) alert("Horario eliminado exitosamente");
});

document.addEventListener("DOMContentLoaded", async () => {
    await cargarOpciones("/sala", "idSala");
    await cargarOpciones("/sala", "idSalaConsulta");
    await cargarOpciones("/programa", "idPrograma");
});
