/**
 * MEDICOS.JS - SECTOR 404
 * Funcionalidades para el módulo de médicos
 * Comentarios y estilo sencillos para quien está aprendiendo JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Búsqueda en tiempo real
    configurarBusqueda();
    
    // Validación del formulario
    validarFormulario();
    
    // Animaciones
    animarTabla();
    
});

// Si CRUD está deshabilitado en el servidor, mantener la variable en false
// Usamos 'var' para que sea más familiar para principiantes
var CRUD_ENABLED = false;

/**
 * Configurar búsqueda en tiempo real en la tabla
 */
function configurarBusqueda() {
    const inputBuscar = document.querySelector('.buscar-tabla');
    const tabla = document.getElementById('tablaMedicos');
    
    if (inputBuscar && tabla) {
        inputBuscar.addEventListener('keyup', function() {
            const textoBusqueda = this.value.toLowerCase();
            const filas = tabla.querySelectorAll('tbody tr');
            
            filas.forEach(fila => {
                const texto = fila.textContent.toLowerCase();
                
                if (texto.includes(textoBusqueda)) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        });
    }
}

/**
 * Validación del formulario antes de enviar
 */
function validarFormulario() {
    const form = document.getElementById('formMedico');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            const nombre = document.getElementById('nombre').value.trim();
            const cedula = document.getElementById('cedula').value.trim();
            const especialidad = document.getElementById('especialidad_id').value;
            
            // Validar nombre
            if (nombre.length < 3) {
                e.preventDefault();
                mostrarAlerta('El nombre debe tener al menos 3 caracteres', 'warning');
                return false;
            }
            
            // Validar cédula
            if (cedula.length < 4) {
                e.preventDefault();
                mostrarAlerta('La cédula debe tener al menos 4 caracteres', 'warning');
                return false;
            }
            
            // Validar especialidad
            if (especialidad === '' || especialidad === '0') {
                e.preventDefault();
                mostrarAlerta('Debes seleccionar una especialidad', 'warning');
                return false;
            }
            
            // Validar teléfono (opcional pero si se llena debe ser válido)
            const telefono = document.getElementById('telefono').value.trim();
            if (telefono !== '' && !validarTelefono(telefono)) {
                e.preventDefault();
                mostrarAlerta('El formato del teléfono no es válido', 'warning');
                return false;
            }
            
            // Validar correo (opcional pero si se llena debe ser válido)
            const correo = document.getElementById('correo').value.trim();
            if (correo !== '' && !validarCorreo(correo)) {
                e.preventDefault();
                mostrarAlerta('El formato del correo no es válido', 'warning');
                return false;
            }
        });
    }
}

/**
 * Validar formato de teléfono
 */
function validarTelefono(telefono) {
    const regex = /^[0-9]{10}$/;
    return regex.test(telefono.replace(/[\s\-\(\)]/g, ''));
}

/**
 * Validar formato de correo
 */
function validarCorreo(correo) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(correo);
}

/**
 * Mostrar alerta personalizada
 */
function mostrarAlerta(mensaje, tipo = 'info') {
    const alerta = document.createElement('div');
    alerta.className = `alert alert-${tipo} alert-dismissible fade show`;
    alerta.role = 'alert';
    alerta.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const contenedor = document.querySelector('.principal');
    const primerTarjeta = contenedor.querySelector('.tarjeta');
    
    contenedor.insertBefore(alerta, primerTarjeta);
    
    // Auto-cerrar después de 5 segundos
    setTimeout(() => {
        alerta.remove();
    }, 5000);
    
    // Scroll al inicio para ver la alerta
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

/**
 * Animar filas de la tabla al cargar
 */
function animarTabla() {
    const filas = document.querySelectorAll('#tablaMedicos tbody tr');
    
    filas.forEach((fila, index) => {
        fila.style.opacity = '0';
        fila.style.transform = 'translateX(-20px)';
        
        setTimeout(() => {
            fila.style.transition = 'all 0.3s ease';
            fila.style.opacity = '1';
            fila.style.transform = 'translateX(0)';
        }, index * 50);
    });
}

/**
 * Formato automático para teléfono
 */
const inputTelefono = document.getElementById('telefono');
if (inputTelefono) {
    inputTelefono.addEventListener('input', function(e) {
        let valor = e.target.value.replace(/\D/g, '');
        
        if (valor.length > 10) {
            valor = valor.substring(0, 10);
        }
        
        e.target.value = valor;
    });
}

/**
 * Confirmar eliminación con mensaje personalizado
 */
// Interceptar acciones CRUD en la UI. Si CRUD no está activo, mostramos una notificación.
document.addEventListener('click', function(e) {
    const target = e.target.closest('.crud-eliminar, .crud-editar');
    if (!target) return;
    if (!CRUD_ENABLED) {
        e.preventDefault();
        const id = target.getAttribute('data-id');
        const accion = target.classList.contains('crud-eliminar') ? 'eliminar' : 'editar';
        mostrarAlerta(`CRUD deshabilitado (demo). Acción: ${accion} ; Id: ${id}`, 'warning');
        return false;
    }
    // Si CRUD_ENABLED == true, las acciones pueden enviar formularios o navegar.
});