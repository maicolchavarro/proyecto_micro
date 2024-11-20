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
    if (!response.ok) {
        alert(`Error en la solicitud: ${response.statusText}`);
        return null;
    }
    return await response.json();
}

// Cargar opciones en desplegables
async function cargarOpciones(endpoint, selectId) {
    const result = await apiRequest(endpoint, "GET");
    if (!result) return;

    const select = document.getElementById(selectId);
    select.innerHTML = '<option value="">Seleccionar</option>';
    result.forEach(item => {
        const option = document.createElement("option");
        option.value = item.id;
        option.textContent = item.nombre || item.descripcion; // Si el item tiene nombre o descripcion
        select.appendChild(option);
    });
}

// Cargar datos al iniciar la página
document.addEventListener("DOMContentLoaded", async () => {
    await cargarOpciones("/programa", "idPrograma");
    await cargarOpciones("/programa", "idProgramaConsulta");
    await cargarOpciones("/sala", "idSala");
    await cargarOpciones("/responsable", "idResponsable");
});

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
    if (result) {
        alert("Ingreso registrado exitosamente");
        console.log(result);
    }
});

// Registrar salida
document.getElementById("formRegistrarSalida").addEventListener("submit", async (e) => {
    e.preventDefault();
    const id = document.getElementById("idIngresoSalida").value;
    const body = { horaSalida: document.getElementById("horaSalida").value };
    
    const result = await apiRequest(`/ingresos/${id}/salida`, "PUT", body);
    if (result) {
        alert("Salida registrada exitosamente");
        console.log(result);
    }
});

// Consultar ingresos
document.getElementById("formConsultarIngresos").addEventListener("submit", async (e) => {
    e.preventDefault();
    const fechaInicio = document.getElementById("fechaInicio").value;
    const fechaFin = document.getElementById("fechaFin").value;
    const idPrograma = document.getElementById("idProgramaConsulta").value;
    
    const query = `?fechaInicio=${fechaInicio}&fechaFin=${fechaFin}&idPrograma=${idPrograma}`;
    const result = await apiRequest(`/ingresos/consulta${query}`);
    
    const ingresosResultado = document.getElementById("ingresosResultado");
    if (result) {
        ingresosResultado.innerText = JSON.stringify(result, null, 2);
    } else {
        ingresosResultado.innerText = "No se encontraron ingresos para los criterios de búsqueda.";
    }
});
