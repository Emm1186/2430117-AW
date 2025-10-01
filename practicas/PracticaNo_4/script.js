// Array para guardar los usuarios (empieza vacio claroo)
let usuarios = [];

// la del Registro
document.getElementById("formulario").addEventListener("submit", function(event){
  event.preventDefault();

  let email = document.getElementById("email").value;
  let password = document.getElementById("password").value;

  usuarios.push({email: email, password: password});
  console.log("Registrado:", usuarios);
});

// La parte de login
document.getElementById("loginForm").addEventListener("submit", function(event){
  event.preventDefault();

  let email = document.getElementById("loginEmail").value;
  let password = document.getElementById("loginPassword").value;

  // Busca en el array
  let usuario = usuarios.find(u => u.email === email && u.password === password);

  if(usuario){
    document.getElementById("mensaje").textContent = `Bienvenido ${usuario.email}`;
  } else {
    document.getElementById("mensaje").textContent = "Usuario o contraseña incorrectos";
  }
});