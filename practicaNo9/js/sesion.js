// Lee la sesión guardada por el login
const sesionTexto = localStorage.getItem('session');
if(!sesionTexto){
  // si no hay sesión → regresar a login
  location.replace('index.html');
} else {
  const sesion = JSON.parse(sesionTexto);

  // Muestra nombre o correo en el header
  const textoUsuario = document.getElementById('textoUsuario');
  if(textoUsuario){
    textoUsuario.textContent = sesion.nombre && sesion.nombre.trim() !== '' ? sesion.nombre : sesion.correo;
  }

  // Botón de cerrar sesión
  const botonSalir = document.getElementById('botonSalir');
  if(botonSalir){
    botonSalir.addEventListener('click', ()=>{
      localStorage.removeItem('session');
      location.href = 'index.html';
    });
  }
}