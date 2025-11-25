// expedientes.js - Gestión de expedientes médicos (sin API)

// Búsqueda en tiempo real
document.getElementById('buscarPaciente')?.addEventListener('input', function (e) {
    const filtro = e.target.value.toLowerCase();
    const filas = document.querySelectorAll('#tablaExpedientes tbody tr');

    filas.forEach(fila => {
        const paciente = fila.cells[1]?.textContent.toLowerCase() || '';
        if (paciente.includes(filtro)) {
            fila.style.display = '';
        } else {
            fila.style.display = 'none';
        }
    });
});

// Ver detalles de expediente (sin API, usando datos embebidos)
function verExpediente(idExpediente) {
    // Buscar el expediente en los datos embebidos
    const exp = expedientesData.find(e => e.IdExpediente == idExpediente);

    if (exp) {
        const contenido = `
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>Paciente:</strong><br>
                    ${exp.Paciente}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Médico:</strong><br>
                    ${exp.Medico}
                </div>
            </div>
            <div class="mb-3">
                <strong>Fecha de Consulta:</strong><br>
                ${formatearFecha(exp.FechaConsulta)}
            </div>
            <hr>
            <div class="mb-3">
                <strong>Síntomas:</strong><br>
                <p class="text-muted">${exp.Sintomas || 'No especificado'}</p>
            </div>
            <div class="mb-3">
                <strong>Diagnóstico:</strong><br>
                <p class="text-muted">${exp.Diagnostico || 'No especificado'}</p>
            </div>
            <div class="mb-3">
                <strong>Tratamiento:</strong><br>
                <p class="text-muted">${exp.Tratamiento || 'No especificado'}</p>
            </div>
            <div class="mb-3">
                <strong>Receta Médica:</strong><br>
                <p class="text-muted">${exp.RecetaMedica || 'No especificado'}</p>
            </div>
            <div class="mb-3">
                <strong>Notas Adicionales:</strong><br>
                <p class="text-muted">${exp.NotasAdicionales || 'No especificado'}</p>
            </div>
            ${exp.ProximaCita ? `
            <div class="mb-3">
                <strong>Próxima Cita:</strong><br>
                <span class="badge bg-info">${formatearFecha(exp.ProximaCita)}</span>
            </div>
            ` : ''}
        `;

        document.getElementById('contenidoExpediente').innerHTML = contenido;
        const modal = new bootstrap.Modal(document.getElementById('modalVerExpediente'));
        modal.show();
    } else {
        alert('Error: Expediente no encontrado');
    }
}

// Editar expediente (sin API, usando datos embebidos)
function editarExpediente(idExpediente) {
    // Buscar el expediente en los datos embebidos
    const exp = expedientesData.find(e => e.IdExpediente == idExpediente);

    if (exp) {
        document.getElementById('edit_id_expediente').value = exp.IdExpediente;
        document.getElementById('edit_sintomas').value = exp.Sintomas || '';
        document.getElementById('edit_diagnostico').value = exp.Diagnostico || '';
        document.getElementById('edit_tratamiento').value = exp.Tratamiento || '';
        document.getElementById('edit_receta').value = exp.RecetaMedica || '';
        document.getElementById('edit_notas').value = exp.NotasAdicionales || '';

        if (exp.ProximaCita) {
            // Convertir a formato datetime-local
            const fecha = new Date(exp.ProximaCita);
            const fechaLocal = new Date(fecha.getTime() - fecha.getTimezoneOffset() * 60000)
                .toISOString()
                .slice(0, 16);
            document.getElementById('edit_proxima_cita').value = fechaLocal;
        }

        const modal = new bootstrap.Modal(document.getElementById('modalEditarExpediente'));
        modal.show();
    } else {
        alert('Error: Expediente no encontrado');
    }
}

// Formatear fecha
function formatearFecha(fechaStr) {
    const fecha = new Date(fechaStr);
    const dia = String(fecha.getDate()).padStart(2, '0');
    const mes = String(fecha.getMonth() + 1).padStart(2, '0');
    const anio = fecha.getFullYear();
    const horas = String(fecha.getHours()).padStart(2, '0');
    const minutos = String(fecha.getMinutes()).padStart(2, '0');

    return `${dia}/${mes}/${anio} ${horas}:${minutos}`;
}

// Limpiar formulario al cerrar modal
document.getElementById('modalNuevoExpediente')?.addEventListener('hidden.bs.modal', function () {
    this.querySelector('form').reset();
});
