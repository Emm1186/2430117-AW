let formulario = document.getElementById("registro");

formulario.onsubmit = function(evento) {
  evento.preventDefault(); // para que noo se recargue la página

  let nombre = document.getElementById("nombre").value;
  let correo = document.getElementById("email").value;
  let contrasena = document.getElementById("contrasena").value;
  let confirmar = document.getElementById("confirmar").value;

  if (nombre === "" || correo === "" || contrasena === "" || confirmar === "") {
    alert("Por favor, llena todos los campos");
    return;
  }
  if (correo.includes("@") === false) {
    alert("El correo no es válido");
    return;
  }
  if (contrasena.length < 6) {
    alert("La contraseña es muy corta (mínimo 6 caracteres)");
    return;
  }
  if (contrasena !== confirmar) {
    alert("Las contraseñas no coinciden");
    return;
  }
  alert("¡Bienvenido " + nombre + "!");
}