// registro.js
const formRegistro = document.getElementById('formRegistro');
const mensajeError = document.getElementById('mensajeError');
const mensajeOk    = document.getElementById('mensajeOk');

formRegistro.addEventListener('submit', (e)=>{
  e.preventDefault();

  const Nombre     = document.getElementById('regNombre').value.trim();
  const Correo     = document.getElementById('regCorreo').value.trim();
  const Contrasena = document.getElementById('regPass').value.trim();

  // Validación simple
  if(!Correo || !Contrasena){
    mensajeOk.style.display = 'none';
    mensajeError.textContent = 'Completa correo y contraseña';
    mensajeError.style.display = 'block';
    return;
  }

  const res = UsersDB.addUser({ Correo, Contrasena, Nombre, Rol: "Recepcionista", Activo: 1 });

  if(!res.ok){
    // Ya existe
    mensajeOk.style.display = 'none';
    mensajeError.textContent = 'El correo ya existe';
    mensajeError.style.display = 'block';
    return;
  }

  // Éxito
  mensajeError.style.display = 'none';
  mensajeOk.style.display = 'block';

  // Redirigir a login
  setTimeout(()=> { location.href = 'index.html'; }, 900);
});