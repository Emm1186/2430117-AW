-- Script para agregar usuarios predefinidos con roles
USE sector404;

INSERT INTO usuarios (Correo, Contrasena, Nombre, Rol, Activo) 
VALUES ('admin@admin', 'admin', 'Administrador Principal', 'Admin', 1)
ON DUPLICATE KEY UPDATE Rol='Admin', Contrasena='admin';

-- 3. Insertar Secretaria (secre@secre / secre)
INSERT INTO usuarios (Correo, Contrasena, Nombre, Rol, Activo) 
VALUES ('secre@secre', 'secre', 'Secretaria General', 'Secretaria', 1)
ON DUPLICATE KEY UPDATE Rol='Secretaria', Contrasena='secre';

-- 4. Insertar un Paciente de prueba (opcional)
INSERT INTO usuarios (Correo, Contrasena, Nombre, Rol, Activo) 
VALUES ('paciente@test.com', '12345', 'Paciente Prueba', 'Paciente', 1)
ON DUPLICATE KEY UPDATE Rol='Paciente';
