
const regForm = document.getElementById('registerForm');
const regErr = document.getElementById('regError');
const regOk = document.getElementById('regOk');

regForm.addEventListener('submit', (e)=>{
  e.preventDefault();
  const Nombre = document.getElementById('regNombre').value.trim();
  const Correo = document.getElementById('regCorreo').value.trim();
  const Contrasena = document.getElementById('regPass').value.trim();

  const res = UsersDB.addUser({ Correo, Contrasena, Nombre, Rol: "Recepcionista", Activo: 1 });
  if(!res.ok){
    regErr.style.display = 'block';
    regOk.style.display = 'none';
    return;
  }
  regErr.style.display = 'none';
  regOk.style.display = 'block';
  setTimeout(()=> location.href = 'index.html', 900);
});