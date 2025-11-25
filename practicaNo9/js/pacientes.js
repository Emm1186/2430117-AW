// pacientes.js - Gestión de pacientes

// Búsqueda en tiempo real
document.getElementById('buscarPaciente')?.addEventListener('input', function (e) {
    const filtro = e.target.value.toLowerCase();
    const filas = document.querySelectorAll('#tablaPacientes tbody tr');

    filas.forEach(fila => {
        const nombre = fila.cells[0]?.textContent.toLowerCase() || '';
        const curp = fila.cells[1]?.textContent.toLowerCase() || '';
        const telefono = fila.cells[2]?.textContent.toLowerCase() || '';

        if (nombre.includes(filtro) || curp.includes(filtro) || telefono.includes(filtro)) {
            fila.style.display = '';
        } else {
            fila.style.display = 'none';
        }
    });
});

// Ver detalles completos del paciente
function verPaciente(idPaciente) {
    const pac = pacientesData.find(p => p.IdPaciente == idPaciente);

    if (pac) {
        // Calcular edad
        let edadTexto = '-';
        if (pac.FechaNacimiento) {
            const fechaNac = new Date(pac.FechaNacimiento);
            const hoy = new Date();
            let edad = hoy.getFullYear() - fechaNac.getFullYear();
            const mes = hoy.getMonth() - fechaNac.getMonth();
            if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) {
                edad--;
            }
            edadTexto = edad + ' años';
        }

        const contenido = `
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>Nombre Completo:</strong><br>
                    ${pac.NombreCompleto}
                </div>
                <div class="col-md-3 mb-3">
                    <strong>CURP:</strong><br>
                    ${pac.CURP || '-'}
                </div>
                <div class="col-md-3 mb-3">
                    <strong>Edad:</strong><br>
                    ${edadTexto}
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <strong>Sexo:</strong><br>
                    ${pac.Sexo == 'M' ? 'Masculino' : (pac.Sexo == 'F' ? 'Femenino' : '-')}
                </div>
                <div class="col-md-4 mb-3">
                    <strong>Teléfono:</strong><br>
                    ${pac.Telefono}
                </div>
                <div class="col-md-4 mb-3">
                    <strong>Correo:</strong><br>
                    ${pac.CorreoElectronico || '-'}
                </div>
            </div>
            
            <div class="mb-3">
                <strong>Dirección:</strong><br>
                <p class="text-muted">${pac.Direccion || '-'}</p>
            </div>
            
            <hr>
            <h6>Contacto de Emergencia</h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>Nombre:</strong><br>
                    ${pac.ContactoEmergencia || '-'}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Teléfono:</strong><br>
                    ${pac.TelefonoEmergencia || '-'}
                </div>
            </div>
            
            <hr>
            <h6>Información Médica</h6>
            <div class="mb-3">
                <strong>Alergias:</strong><br>
                <p class="text-muted">${pac.Alergias || 'Ninguna registrada'}</p>
            </div>
            <div class="mb-3">
                <strong>Antecedentes Médicos:</strong><br>
                <p class="text-muted">${pac.AntecedentesMedicos || 'Ninguno registrado'}</p>
            </div>
            
            <div class="mb-3">
                <strong>Fecha de Registro:</strong><br>
                <span class="badge bg-info">${formatearFecha(pac.FechaRegistro)}</span>
            </div>
        `;

        document.getElementById('contenidoPaciente').innerHTML = contenido;
        const modal = new bootstrap.Modal(document.getElementById('modalVerPaciente'));
        modal.show();
    } else {
        alert('Error: Paciente no encontrado');
    }
}

// Formatear fecha
function formatearFecha(fechaStr) {
    if (!fechaStr) return '-';
    const fecha = new Date(fechaStr);
    const dia = String(fecha.getDate()).padStart(2, '0');
    const mes = String(fecha.getMonth() + 1).padStart(2, '0');
    const anio = fecha.getFullYear();
    return `${dia}/${mes}/${anio}`;
}

// Validar CURP (opcional)
document.querySelector('input[name="curp"]')?.addEventListener('blur', function () {
    const curp = this.value.toUpperCase();
    if (curp && curp.length !== 18) {
        alert('El CURP debe tener exactamente 18 caracteres');
    }
    this.value = curp;
});

// Mostrar/ocultar información de cuenta
document.getElementById('crearCuenta')?.addEventListener('change', function () {
    const correoInput = document.querySelector('input[name="correo"]');
    if (this.checked) {
        correoInput.required = true;
        correoInput.parentElement.querySelector('label').innerHTML = 'Correo Electrónico *';
        alert('Se creará una cuenta de usuario con rol "Paciente". Se generará una contraseña temporal.');
    } else {
        correoInput.required = false;
        correoInput.parentElement.querySelector('label').innerHTML = 'Correo Electrónico';
    }
});
