// Login (index.js)
const formulario     = document.getElementById('loginForm');
const mensajeError   = document.getElementById('errorMessage');
const mensajeExito   = document.getElementById('successMessage');

formulario.addEventListener('submit', (evento) => {
  evento.preventDefault();

  const correo     = document.getElementById('username').value.trim();
  const contrasena = document.getElementById('password').value.trim();

  const usuario = UsersDB.findByCredentials(correo, contrasena);

  if (usuario) {
    // Mostrar OK
    mensajeError.style.display = 'none';
    mensajeExito.style.display = 'block';

    // Guardar sesión
    localStorage.setItem('session', JSON.stringify({
      idUsuario: usuario.IdUsuario,
      correo:    usuario.Correo,
      nombre:    usuario.Nombre || "",
      ts:        Date.now()
    }));

    // === REDIRECCIÓN AL DASHBOARD ===
    // Si index.html y dashboard.html están en el MISMO folder (htmls/)
    // usa ruta relativa directa:
    window.location.replace('dashboard.html');   // no deja volver con "atrás"
    // o si prefieres permitir volver con "atrás":
    // window.location.href = 'dashboard.html';

  } else {
    // Mostrar error
    mensajeExito.style.display = 'none';
    mensajeError.style.display = 'block';
  }
});