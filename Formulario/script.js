// Obtener elementos medinate ID
const inputMatricula = document.getElementById("input-matricula");
const inputNombre = document.getElementById("input-nombre");
const selectCarrera = document.getElementById("select-carrera");
const inputEmail = document.getElementById("input-email");
const inputTelefono = document.getElementById("input-telefono");

const btnGuardar = document.getElementById("btn-guardar");
const btnLimpiar = document.getElementById("btn-limpiar");

const tablaBody = document.getElementById("tabla-body");
const tablaInfo = document.getElementById("tabla-info");

let alumnos = [];


btnGuardar.addEventListener("click", () => {
  const matricula = inputMatricula.value;
  const nombre = inputNombre.value;
  const carrera = selectCarrera.value;
  const email = inputEmail.value;
  const telefono = inputTelefono.value;

  if (!matricula || !nombre || carrera === "Seleccionar carrera" || !email || !telefono) {
    alert("Por favor, completa todos los campos.");
    return;
  }

  const nuevoAlumno = { matricula, nombre, carrera, email, telefono };
  alumnos.push(nuevoAlumno);

  mostrarAlumnos();
  limpiarFormulario();
});

function mostrarAlumnos() {
  tablaBody.innerHTML = "";

  alumnos.forEach((alumno, index) => {
    const fila = `
      <tr>
        <td>${alumno.matricula}</td>
        <td>${alumno.nombre}</td>
        <td>${alumno.carrera}</td>
        <td>${alumno.email}</td>
        <td>${alumno.telefono}</td>
        <td>
          <button class="btn btn-danger btn-sm" onclick="eliminarAlumno(${index})">Eliminar</button>
        </td>
      </tr>
    `;
    tablaBody.innerHTML += fila;
  });

  tablaInfo.textContent = `Mostrando ${alumnos.length} de ${alumnos.length} registros`;
}

function eliminarAlumno(indice) {
  alumnos.splice(indice, 1);
  mostrarAlumnos();
}


function limpiarFormulario() {
  inputMatricula.value = "";
  inputNombre.value = "";
  selectCarrera.value = "Seleccionar carrera";
  inputEmail.value = "";
  inputTelefono.value = "";
}
