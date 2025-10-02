//  VARIABLES GLOBALES 
let usuarios = []; // lista de usuarios en memoria
let tareas = [];   // lista de tareas en memoria
let currentUser = null;
let editTaskId = null; // id de tarea en edición

// FUNCIONES DE USUARIOS 
function registrar() {
  const email = document.getElementById("registerEmail").value;
  const password = document.getElementById("registerPassword").value;

  if (!email || !password) {
    alert("Llena todos los campos");
    return;
  }
  if (usuarios.find(u => u.email === email)) {
    alert("Ese correo ya está registrado");
    return;
  }

  usuarios.push({ email, password });
  alert("Usuario registrado con éxito");
  mostrarLogin();
}

function login() {
  const email = document.getElementById("loginEmail").value;
  const password = document.getElementById("loginPassword").value;

  const user = usuarios.find(u => u.email === email && u.password === password);
  if (user) {
    currentUser = user;
    showPostLogin(email);
  } else {
    alert("Usuario o contraseña incorrectos");
  }
}

function mostrarRegistro() {
  document.getElementById("loginForm").classList.add("oculto");
  document.getElementById("registerForm").classList.remove("oculto");
}

function mostrarLogin() {
  document.getElementById("registerForm").classList.add("oculto");
  document.getElementById("loginForm").classList.remove("oculto");
}

function showPostLogin(email) {
  document.getElementById("loginForm").classList.add("oculto");
  document.getElementById("registerForm").classList.add("oculto");
  document.getElementById("mensaje").classList.remove("oculto");
  document.getElementById("mensaje").innerText = `Bienvenido, ${email}`;
  document.getElementById("taskManager").classList.remove("oculto");
  renderTareas();
}

// FUNCIONES DE TAREAS 
function handleTaskSubmit(e) {
  e.preventDefault();

  const tarea = document.getElementById("tarea").value;
  const descripcion = document.getElementById("descripcion").value;
  const prioridad = document.getElementById("prioridad").value;
  const estado = document.getElementById("estado").value;
  const fecha = document.getElementById("fecha").value;

  if (!tarea || !descripcion || !fecha) {
    alert("Completa todos los campos obligatorios");
    return;
  }

  if (editTaskId) {
    const idx = tareas.findIndex(t => t.id === editTaskId);
    tareas[idx] = { ...tareas[idx], tarea, descripcion, prioridad, estado, fecha };
    editTaskId = null;
  } else {
    const nueva = {
      id: Date.now().toString(),
      tarea, descripcion, prioridad, estado, fecha,
      owner: currentUser.email
    };
    tareas.push(nueva);
  }

  document.getElementById("taskForm").reset();
  renderTareas();
}

function renderTareas() {
  const taskList = document.getElementById("taskList");
  taskList.innerHTML = "";

  const visibles = tareas.filter(t => t.owner === currentUser.email);
  visibles.forEach(t => {
    const row = `
      <tr>
        <td>${t.tarea}</td>
        <td>${t.descripcion}</td>
        <td>${t.prioridad}</td>
        <td>${t.estado}</td>
        <td>${t.fecha}</td>
        <td>
          <button onclick="editarTarea('${t.id}')">Editar</button>
          <button onclick="borrarTarea('${t.id}')">Borrar</button>
        </td>
      </tr>
    `;
    taskList.innerHTML += row;
  });
}

function borrarTarea(id) {
  tareas = tareas.filter(t => t.id !== id);
  renderTareas();
}

function editarTarea(id) {
  const t = tareas.find(t => t.id === id);
  if (!t) return;
  document.getElementById("tarea").value = t.tarea;
  document.getElementById("descripcion").value = t.descripcion;
  document.getElementById("prioridad").value = t.prioridad;
  document.getElementById("estado").value = t.estado;
  document.getElementById("fecha").value = t.fecha;
  editTaskId = id;
}

// EVENTOS 
document.getElementById("btnLogin").addEventListener("click", login);
document.getElementById("btnRegistro").addEventListener("click", registrar);
document.getElementById("linkRegistro").addEventListener("click", mostrarRegistro);
document.getElementById("linkLogin").addEventListener("click", mostrarLogin);
document.getElementById("taskForm").addEventListener("submit", handleTaskSubmit);