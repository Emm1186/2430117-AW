
(function(global){
  const KEY = "clinic_Usuarios";
  function readUsers(){
    try { const raw = localStorage.getItem(KEY); return raw ? JSON.parse(raw) : []; }
    catch(e){ return []; }
  }
  function writeUsers(list){ localStorage.setItem(KEY, JSON.stringify(list)); }
  function seed(){
    const list = readUsers();
    if(!list.length){
      writeUsers([
        { IdUsuario: 1, Correo: "admin@clinic.com", Contrasena: "admin123", Nombre: "Administrador", Rol: "Admin", Activo: 1 }
      ]);
    }
  }
  function nextId(){
    const l = readUsers();
    return l.length ? Math.max(...l.map(u => Number(u.IdUsuario)||0)) + 1 : 1;
  }
  function addUser({Correo, Contrasena, Nombre="", Rol="Recepcionista", Activo=1}){
    const users = readUsers();
    if(users.some(u => u.Correo.toLowerCase() === String(Correo).toLowerCase())) return { ok:false, error:"EXISTS" };
    const u = { IdUsuario: nextId(), Correo, Contrasena, Nombre, Rol, Activo };
    users.push(u); writeUsers(users);
    return { ok:true, user: u };
  }
  function findByCredentials(correo, pass){
    const u = readUsers().find(u => u.Correo.toLowerCase() === String(correo).toLowerCase() && u.Contrasena === pass && u.Activo !== 0);
    return u || null;
  }
  seed();
  global.UsersDB = { readUsers, writeUsers, addUser, findByCredentials };
})(window);