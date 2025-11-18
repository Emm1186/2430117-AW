// Esperar a que cargue el DOM
document.addEventListener('DOMContentLoaded', function() {
    
    // Mensaje de bienvenida al cargar
    console.log('Dashboard cargado correctamente');
    
    // Actualizar hora en tiempo real (opcional)
    actualizarHora();
    setInterval(actualizarHora, 1000);
    
    // Animación de entrada para las tarjetas
    animarTarjetas();
    
});

/**
 * Actualizar reloj en tiempo real (opcional - puedes agregarlo al header)
 */
function actualizarHora() {
    const ahora = new Date();
    const opciones = { 
        hour: '2-digit', 
        minute: '2-digit', 
        second: '2-digit',
        hour12: false 
    };
    const horaActual = ahora.toLocaleTimeString('es-MX', opciones);
    
    // Si agregas un elemento para mostrar la hora:
    const elementoHora = document.getElementById('hora-actual');
    if (elementoHora) {
        elementoHora.textContent = horaActual;
    }
}

/**
 * Animar entrada de tarjetas de estadísticas
 */
function animarTarjetas() {
    const tarjetas = document.querySelectorAll('.stat-card');
    
    tarjetas.forEach((tarjeta, index) => {
        tarjeta.style.opacity = '0';
        tarjeta.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            tarjeta.style.transition = 'all 0.5s ease';
            tarjeta.style.opacity = '1';
            tarjeta.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

/**
 * Función para mostrar notificaciones tipo toast
 */
function mostrarNotificacion(mensaje, tipo = 'info') {
    // Crear elemento de notificación
    const notif = document.createElement('div');
    notif.className = `alerta-notificacion alerta-${tipo}`;
    notif.textContent = mensaje;
    
    // Estilos inline para la notificación
    notif.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        background: ${tipo === 'exito' ? '#28a745' : tipo === 'error' ? '#dc3545' : '#17a2b8'};
        color: white;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        z-index: 9999;
        font-weight: 600;
        animation: slideInRight 0.3s ease;
    `;
    
    document.body.appendChild(notif);
    
    // Eliminar después de 3 segundos
    setTimeout(() => {
        notif.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notif.remove(), 300);
    }, 3000);
}

/**
 * Confirmar antes de cerrar sesión
 */
const btnCerrarSesion = document.querySelector('a[href*="logout"]');
if (btnCerrarSesion) {
    btnCerrarSesion.addEventListener('click', function(e) {
        if (!confirm('¿Estás seguro de que deseas cerrar sesión?')) {
            e.preventDefault();
        }
    });
}

/**
 * Función auxiliar para formato de números
 */
function formatearNumero(numero) {
    return new Intl.NumberFormat('es-MX').format(numero);
}

/**
 * Función auxiliar para formato de moneda
 */
function formatearMoneda(cantidad) {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN'
    }).format(cantidad);
}

// Agregar animaciones CSS dinámicamente
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);