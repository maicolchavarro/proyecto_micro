const API_URL = "http://localhost:8000/api";

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
    ingresosResultado.innerHTML = ""; 

    if (result && result.length > 0) {
        const table = document.createElement("table");
        table.border = "1";
        table.style.width = "100%";
        const thead = document.createElement("thead");
        const tbody = document.createElement("tbody");

        const headers = [
            "ID Ingreso",
            "Código Estudiante",
            "Nombre Estudiante",
            "Fecha Ingreso",
            "Hora Ingreso",
            "Hora Salida",
        ];
        const headerRow = document.createElement("tr");
        headers.forEach(header => {
            const th = document.createElement("th");
            th.textContent = header;
            headerRow.appendChild(th);
        });
        thead.appendChild(headerRow);

        result.forEach(item => {
            const row = document.createElement("tr");

            [
                item.id,
                item.codigoEstudiante,
                item.nombreEstudiante,
                item.fechaIngreso,
                item.horaIngreso,
                item.horaSalida || "N/A",
            ].forEach(text => {
                const td = document.createElement("td");
                td.textContent = text;
                row.appendChild(td);
            });

            

            tbody.appendChild(row);
        });

        table.appendChild(thead);
        table.appendChild(tbody);
        ingresosResultado.appendChild(table);
    } else {
        ingresosResultado.innerText = "No se encontraron ingresos para los criterios de búsqueda.";
    }
});

// Modificar ingreso
document.getElementById("formModificarIngreso").addEventListener("submit", async (e) => {
    e.preventDefault();
    const idIngreso = document.getElementById("idIngresoModificar").value;
    const body = {
        codigoEstudiante: document.getElementById("codigoEstudianteModificar").value,
        nombreEstudiante: document.getElementById("nombreEstudianteModificar").value,
    };

    const result = await apiRequest(`/ingresos/${idIngreso}`, "PUT", body);
    if (result) {
        alert("Ingreso modificado exitosamente");
        console.log(result);
    }
});
