// Configuración de la URL base de la API
const API_URL = "http://localhost:8000/api";

// Función genérica para realizar solicitudes a la API
async function apiRequest(endpoint, method = "GET", body = null) {
    const options = {
        method,
        headers: { "Content-Type": "application/json" },
        body: body ? JSON.stringify(body) : null,
    };

    const response = await fetch(`${API_URL}${endpoint}`, options);
    return response.json();
}

// Registrar ingreso
document.getElementById("formRegistrarIngreso").addEventListener("submit", async (e) => {
    e.preventDefault();
    const body = {
        codigoEstudiante: document.getElementById("codigoEstudiante").value,
        nombreEstudiante: document.getElementById("nombreEstudiante").value,
        idPrograma: document.getElementById("idPrograma").value,
        fechaIngreso: document.getElementById("fechaIngreso").value,
        horaIngreso: document.getElementById("horaIngreso").value,
        idSala: document.getElementById("idSala").value,
        idResponsable: document.getElementById("idResponsable").value,
    };
    const result = await apiRequest("/ingresos", "POST", body);
    alert(JSON.stringify(result));
});

// Registrar salida
document.getElementById("formRegistrarSalida").addEventListener("submit", async (e) => {
    e.preventDefault();
    const id = document.getElementById("idIngresoSalida").value;
    const body = { horaSalida: document.getElementById("horaSalida").value };
    const result = await apiRequest(`/ingresos/${id}/salida`, "PUT", body);
    alert(JSON.stringify(result));
});

// Consultar ingresos
document.getElementById("formConsultarIngresos").addEventListener("submit", async (e) => {
    e.preventDefault();
    const fechaInicio = document.getElementById("fechaInicio").value;
    const fechaFin = document.getElementById("fechaFin").value;
    const idPrograma = document.getElementById("idProgramaConsulta").value;
    const query = `?fechaInicio=${fechaInicio}&fechaFin=${fechaFin}&idPrograma=${idPrograma}`;
    const result = await apiRequest(`/ingresos/consulta${query}`);
    document.getElementById("ingresosResultado").innerText = JSON.stringify(result, null, 2);
});

// Registrar horario
document.getElementById("formRegistrarHorario").addEventListener("submit", async (e) => {
    e.preventDefault();
    const body = {
        idSala: document.getElementById("idSalaHorario").value,
        dia: document.getElementById("diaHorario").value,
        materia: document.getElementById("materiaHorario").value,
        horaInicio: document.getElementById("horaInicioHorario").value,
        horaFin: document.getElementById("horaFinHorario").value,
        idPrograma: document.getElementById("idProgramaHorario").value,
    };
    const result = await apiRequest("/horarios-salas", "POST", body);
    alert(JSON.stringify(result));
});

// Consultar horarios
document.getElementById("formConsultarHorarios").addEventListener("submit", async (e) => {
    e.preventDefault();
    const idSala = document.getElementById("idSalaConsulta").value;
    const dia = document.getElementById("diaConsulta").value;
    const query = `?idSala=${idSala}&dia=${dia}`;
    const result = await apiRequest(`/horarios-salas${query}`);
    document.getElementById("horariosResultado").innerText = JSON.stringify(result, null, 2);
});
