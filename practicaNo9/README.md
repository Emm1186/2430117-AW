# 🏥 Sector 404 - Sistema de Gestión Médica

Sistema de gestión médica desarrollado en PHP con MySQL para administrar pacientes, médicos, especialidades, citas, pagos y servicios.

---

## 🚀 Acceso Rápido

<div style="text-align: center; margin: 20px 0;">

### [🔐 Ir a Login](http://134.209.49.200/2430117-AW/practicaNo9/Entrada/login.php)

**Click en el botón arriba para acceder al sistema**

</div>

---

## 📋 Descripción General

**Sector 404** es una aplicación web para la administración de sistemas médicos. Permite:

- ✅ Gestión de usuarios con autenticación
- ✅ CRUD completo de médicos, especialidades, servicios y pagos
- ✅ Control de pacientes y citas
- ✅ Registro de bitácora de acceso
- ✅ Interfaz responsiva con Bootstrap 5
- ✅ Comentarios en español para facilitar aprendizaje

---

## 🛠 Tecnologías Utilizadas

- **Backend**: PHP 8.x
- **Base de Datos**: MySQL / MariaDB
- **Frontend**: HTML5, CSS3, Bootstrap 5.3.8
- **JavaScript**: Vanilla JS para interactividad
- **Control de Versiones**: Git

---

## 📁 Estructura del Proyecto

```
practicaNo9/
├── Conexion/
│   └── conexion.php          # Conexión a BD y funciones auxiliares
├── Entrada/
│   ├── login.php             # Página de inicio de sesión
│   ├── registro.php          # Página de registro de usuarios
│   └── logout.php            # Cerrar sesión
├── js/
│   ├── dashboard.js          # Scripts del dashboard
│   └── medicos.js            # Scripts del módulo de médicos
├── dashboard.php             # Panel principal
├── medicos.php               # CRUD de médicos ✅ FUNCIONAL
├── especialidades.php        # CRUD de especialidades ✅ FUNCIONAL
├── servicios.php             # CRUD de servicios/tarifas ✅ FUNCIONAL
├── pagos.php                 # CRUD de pagos ✅ FUNCIONAL
├── pacientes.php             # CRUD de pacientes (vista básica)
├── agenda.php                # Control de citas (vista básica)
├── reportes.php              # Reportes (vista básica)
├── styles.css                # Estilos globales
├── diagnostico.php           # Script de diagnóstico del servidor
└── README.md                 # Este archivo

```

---

## 🔐 Autenticación

El sistema utiliza **autenticación con sesiones PHP** y almacenamiento de contraseñas en **texto plano** (configurado según requerimientos de aprendizaje).

### Usuarios de Prueba

| Correo | Contraseña | Rol | Estado |
|--------|-----------|-----|--------|
| `admin@gmail.com` | (hash) | Admin | Activo |
| `secretaria@gmail.com` | (hash) | Recepcionista | Activo |
| `Eem@gmail.com` | (hash) | Recepcionista | Activo |

> 📌 **Nota**: Para obtener las contraseñas en texto plano, consulta con el administrador del sistema o ejecuta el script de diagnóstico.

---

## 📊 Módulos CRUD Funcionales

### ✅ 1. Control de Médicos (`medicos.php`)

**Funcionalidades:**
- Crear nuevo médico
- Editar información del médico
- Eliminar médico (borrado lógico)
- Buscar y filtrar
- Validación de cédula única
- Asociar especialidad

**Campos:**
- Nombre completo
- Cédula profesional
- Especialidad
- Teléfono
- Correo electrónico
- Horario de atención
- Estado (activo/inactivo)

---

### ✅ 2. Especialidades Médicas (`especialidades.php`)

**Funcionalidades:**
- Crear nueva especialidad
- Editar especialidad
- Eliminar especialidad
- Listar todas las especialidades

**Campos:**
- Nombre de especialidad
- Descripción

---

### ✅ 3. Servicios / Tarifas (`servicios.php`)

**Funcionalidades:**
- Crear nuevo servicio
- Editar costo y descripción
- Eliminar servicio
- Asociar a especialidad (opcional)

**Campos:**
- Descripción del servicio
- Costo base
- Especialidad relacionada

---

### ✅ 4. Pagos (`pagos.php`)

**Funcionalidades:**
- Registrar nuevo pago
- Editar información del pago
- Anular pago (cambio de estado, no eliminación física)
- Listar pagos recientes

**Campos:**
- ID de Cita
- ID de Paciente
- Monto
- Método de pago
- Referencia
- Estado del pago

---

## 🗄 Base de Datos

### Conexión

```php
Servidor: localhost
Usuario: sectoruser
Contraseña: TuPasswordFuerteAqui!
Base de Datos: sector404
Charset: utf8mb4
```

### Tablas Principales

| Tabla | Descripción |
|-------|------------|
| `usuarios` | Almacena credenciales y datos de usuarios |
| `controlmedico` | Registro de médicos |
| `especialidades` | Especialidades médicas disponibles |
| `gestortarifas` | Servicios y costos |
| `gestorpagos` | Registro de pagos |
| `controlpacientes` | Datos de pacientes |
| `controlagenda` | Citas programadas |
| `bitacoraacceso` | Log de accesos al sistema |

---

## 🚀 Instalación y Configuración

### Requisitos

- PHP 8.0+
- MySQL 5.7+ o MariaDB 10.4+
- Servidor web (Apache, Nginx)
- Navegador moderno

### Pasos de Instalación

#### 1. **Clonar o descargar el repositorio**

```bash
git clone https://github.com/Emm1186/2430117-AW.git
cd 2430117-AW/practicaNo9
```

#### 2. **Configurar la base de datos**

```bash
# Crear base de datos y usuario (desde terminal MySQL)
mysql -u root -p
```

```sql
CREATE DATABASE IF NOT EXISTS sector404;
CREATE USER 'sectoruser'@'localhost' IDENTIFIED BY 'TuPasswordFuerteAqui!';
GRANT ALL PRIVILEGES ON sector404.* TO 'sectoruser'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### 3. **Importar estructura e datos**

```bash
# Desde la carpeta del proyecto
mysql -u sectoruser -p sector404 < sector404.sql
```

> El archivo `sector404.sql` se encuentra en la raíz del proyecto o en el Desktop.

#### 4. **Configurar credenciales de conexión**

Editar `Conexion/conexion.php`:

```php
$servidor = "localhost";      // Host del servidor
$usuario = "sectoruser";      // Usuario MySQL
$password = "TuPasswordFuerteAqui!";  // Contraseña
$base_datos = "sector404";    // Nombre BD
```

#### 5. **Verificar permisos**

```bash
# Linux/Mac
chmod 755 practicaNo9/
chmod 644 practicaNo9/*.php

# Windows: permisos desde propiedades del archivo
```

#### 6. **Probar sintaxis PHP** (opcional)

```bash
php -l Conexion/conexion.php
php -l Entrada/login.php
php -l dashboard.php
```

---

## 🔍 Script de Diagnóstico

Para verificar el estado del servidor y la conexión a la BD:

```bash
# Acceder en el navegador a:
http://localhost/practicaNo9/diagnostico.php
```

**El script verifica:**
- ✅ Versión de PHP
- ✅ Extensiones disponibles (mysqli, session)
- ✅ Configuración de sesiones
- ✅ Conexión a la base de datos
- ✅ Presencia de tablas
- ✅ Registros de usuarios
- ✅ Permisos de archivos

---

## 📝 Guía de Uso

### 1. Iniciar Sesión

1. Acceder a `login.php`
2. Ingresar correo y contraseña
3. Se registra automáticamente el acceso en `bitacoraacceso`
4. Redirige al `dashboard.php`

### 2. Navegar por el Sistema

Desde el **Dashboard** (página de inicio):
- Panel con estadísticas de pacientes, médicos, citas y especialidades
- Acceso rápido a módulos principales
- Próximas citas programadas

### 3. Gestionar Médicos

1. Ir a **Control de Médicos** desde el menú
2. Completar formulario con datos del médico
3. Seleccionar especialidad del dropdown
4. Click en **Guardar**
5. En la lista, opciones para **Editar** o **Eliminar**

### 4. Gestionar Especialidades

1. Ir a **Especialidades Médicas**
2. Ingresar nombre y descripción
3. Click en **Guardar**
4. Aparece en la lista para seleccionar en otros módulos

### 5. Gestionar Servicios

1. Ir a **Servicios / Tarifas**
2. Ingresar descripción y costo
3. Opcionalmente asociar a una especialidad
4. Click en **Guardar**

### 6. Registrar Pagos

1. Ir a **Pagos**
2. Completar ID de cita, ID de paciente, monto
3. Seleccionar método de pago
4. Click en **Guardar**
5. Para anular: botón **Anular** (no elimina, cambia estado)

---

## 🔒 Control de Acceso

| Rol | Permisos |
|-----|----------|
| **Admin** | Acceso completo a todos los módulos, crear/editar/eliminar |
| **Recepcionista** | Ver información, crear registros (crear/editar limitado) |

Para cambiar rol de un usuario, editar directamente en la BD:

```sql
UPDATE usuarios SET Rol = 'Admin' WHERE IdUsuario = 1;
```

---

## ⚠️ Notas Importantes

### Seguridad

- ⚠️ **Contraseñas en texto plano**: Actualmente configuradas así para aprendizaje. **NO USAR EN PRODUCCIÓN**.
- 🔐 Se recomienda implementar hashing (bcrypt/password_hash) antes de producción.
- 🛡️ Usar HTTPS en servidores públicos.

### Respaldo de Base de Datos

```bash
# Crear backup
mysqldump -u sectoruser -p sector404 > sector404_backup.sql

# Restaurar backup
mysql -u sectoruser -p sector404 < sector404_backup.sql
```

---

## 🐛 Solución de Problemas

### "Error de conexión a la base de datos"

- Verificar credenciales en `Conexion/conexion.php`
- Confirmar que MySQL está corriendo: `mysql -u root -p`
- Ejecutar `diagnostico.php` para más detalles

### "Página en blanco o error 500"

- Revisar permisos de archivos
- Ver logs del servidor: `/var/log/apache2/error.log` (Linux)
- Ejecutar prueba de sintaxis: `php -l archivo.php`

### "Sesión no funciona"

- Verificar `session.save_path` en `diagnostico.php`
- Confirmar permisos de escritura en directorio de sesiones
- Revisar `Conexion/conexion.php` - debe llamar `session_start()` al inicio

### "No aparecen datos en CRUD"

- Confirmar que la base de datos fue importada: `mysql -u sectoruser -p -e "USE sector404; SHOW TABLES;"`
- Ejecutar `diagnostico.php` para verificar conexión
- Revisar nombre de tablas (deben estar en minúsculas)

---

## 📚 Documentación de Código

### Funciones Principales

#### `limpiar_dato($dato)` — `Conexion/conexion.php`

Limpia y escapa entrada de usuario para prevenir inyección SQL.

```php
$correo = limpiar_dato($_POST['correo']);
```

#### `sesion_activa()` — `Conexion/conexion.php`

Verifica si hay sesión activa.

```php
if (!sesion_activa()) {
    header('Location: Entrada/login.php');
    exit;
}
```

### Estructura de Formularios CRUD

Todos los módulos CRUD siguen este patrón:

1. **Verificar sesión**
2. **Procesar POST** (crear/editar)
3. **Procesar GET** (editar/eliminar)
4. **Cargar datos para editar**
5. **Mostrar listado**
6. **HTML con formulario + tabla**

---

## 🎓 Para Estudiantes

Este proyecto fue desarrollado con **comentarios en español** y código sencillo para facilitar el aprendizaje de:

- ✅ Fundamentos de PHP
- ✅ Conexión y consultas a MySQL
- ✅ Sesiones y autenticación
- ✅ CRUD (Create, Read, Update, Delete)
- ✅ Validación de formularios
- ✅ Interfaz con Bootstrap
- ✅ Buenas prácticas de seguridad (prepared statements)

**Recursos útiles:**
- [Documentación PHP](https://www.php.net/manual/es/)
- [Bootstrap 5 Docs](https://getbootstrap.com/docs/5.3/)
- [MySQL Reference](https://dev.mysql.com/doc/)

---

## 📞 Contacto y Soporte

Para reportar bugs, sugerencias o consultas:

- 📧 Correo: emmaguirre@example.com
- 🐙 GitHub: [Emm1186/2430117-AW](https://github.com/Emm1186/2430117-AW)
- 💬 Comentarios en el código disponibles en todos los archivos PHP

---

## 📄 Licencia

Este proyecto está disponible bajo licencia de código abierto. Siéntete libre de usarlo, modificarlo y mejorarlo.

---

## ✨ Historial de Cambios

### Versión 1.0 (Actual)

- ✅ Sistema base con autenticación
- ✅ CRUD funcional: Médicos, Especialidades, Servicios, Pagos
- ✅ Dashboard con estadísticas
- ✅ Interfaz responsiva
- ✅ Comentarios en español
- ✅ Script de diagnóstico
- ✅ Documentación completa

### Próximas Mejoras Planeadas

- 🔄 Migración a contraseñas hasheadas (bcrypt)
- 📱 Aplicación móvil
- 📊 Reportes avanzados con gráficos
- 🔔 Sistema de notificaciones
- 📧 Envío de correos automáticos

---

## 🙏 Agradecimientos

Desarrollado como parte de un proyecto educativo. Gracias a Bootstrap, PHP y la comunidad de desarrollo y a mi mamá.

---

<div style="text-align: center; margin-top: 40px;">

**Última actualización**: 18 de Noviembre, 2025

[⬆ Ir a Login](#-acceso-rápido)

</div>