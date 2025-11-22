// lofica para el calendario de agendar cita

document.addEventListener('DOMContentLoaded', function () {

    const gridCalendario = document.getElementById('gridCalendario');
    const tituloMes = document.getElementById('tituloMes');
    const btnAnterior = document.getElementById('btnAnterior');
    const btnSiguiente = document.getElementById('btnSiguiente');
    const inputFechaHora = document.getElementById('inputFechaHora');

    let fechaActual = new Date();

    // Nombres de meses y días
    const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    const diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

    // Renderizar calendario
    function renderizarCalendario(fecha) {
        gridCalendario.innerHTML = '';

        const mes = fecha.getMonth();
        const anio = fecha.getFullYear();

        tituloMes.textContent = `${meses[mes]} ${anio}`;

        // Cabecera de días
        diasSemana.forEach(dia => {
            const div = document.createElement('div');
            div.className = 'dia-semana';
            div.textContent = dia;
            gridCalendario.appendChild(div);
        });

        // Primer día del mes
        const primerDia = new Date(anio, mes, 1);
        const diaSemanaPrimerDia = primerDia.getDay(); // 0 (Dom) a 6 (Sáb)

        // Último día del mes
        const ultimoDia = new Date(anio, mes + 1, 0);
        const totalDias = ultimoDia.getDate();

        // Rellenar espacios vacíos antes del primer día
        for (let i = 0; i < diaSemanaPrimerDia; i++) {
            const div = document.createElement('div');
            div.className = 'dia-mes vacio';
            gridCalendario.appendChild(div);
        }

        // Renderizar días
        for (let i = 1; i <= totalDias; i++) {
            const div = document.createElement('div');
            div.className = 'dia-mes';

            // Verificar si es hoy
            const hoy = new Date();
            if (i === hoy.getDate() && mes === hoy.getMonth() && anio === hoy.getFullYear()) {
                div.classList.add('hoy');
            }

            // Número del día
            const spanNum = document.createElement('span');
            spanNum.className = 'numero-dia';
            spanNum.textContent = i;
            div.appendChild(spanNum);

            // Buscar citas para este día
            // Formato fecha para comparar: YYYY-MM-DD
            const fechaDiaStr = `${anio}-${(mes + 1).toString().padStart(2, '0')}-${i.toString().padStart(2, '0')}`;

            if (typeof citasRegistradas !== 'undefined') {
                const citasDia = citasRegistradas.filter(c => c.start.startsWith(fechaDiaStr));

                citasDia.forEach(cita => {
                    const chip = document.createElement('div');
                    chip.className = 'evento-chip';
                    chip.textContent = cita.title;
                    chip.title = cita.description; // Tooltip nativo
                    div.appendChild(chip);
                });
            }

            // Click en el día para agendar
            div.addEventListener('click', () => {
                // Abrir modal y prellenar fecha
                // Formato datetime-local: YYYY-MM-DDTHH:MM
                const fechaHoraDefault = `${fechaDiaStr}T09:00`;
                inputFechaHora.value = fechaHoraDefault;

                const modal = new bootstrap.Modal(document.getElementById('modalCita'));
                modal.show();
            });

            gridCalendario.appendChild(div);
        }
    }

    // Eventos botones
    btnAnterior.addEventListener('click', () => {
        fechaActual.setMonth(fechaActual.getMonth() - 1);
        renderizarCalendario(fechaActual);
    });

    btnSiguiente.addEventListener('click', () => {
        fechaActual.setMonth(fechaActual.getMonth() + 1);
        renderizarCalendario(fechaActual);
    });

    // Inicializar
    renderizarCalendario(fechaActual);
});