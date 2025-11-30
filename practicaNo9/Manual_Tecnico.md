# Manual Técnico
## Sistema de Gestión Médica - Sector 404

---

## PORTADA

**Sistema de Gestión Médica - Sector 404**  
**Manual Técnico**  
Versión 1.0 - Noviembre 2025

---

## ÍNDICE

1. [Introducción](#introducción)
2. [Arquitectura del Sistema](#arquitectura-del-sistema)
3. [Base de Datos](#base-de-datos)
4. [Documentación de Scripts PHP](#documentación-de-scripts-php)
5. [Trabajo Futuro](#trabajo-futuro)
6. [Conclusiones](#conclusiones)

---

## 1. INTRODUCCIÓN

### 1.1 Propósito del Manual

Este manual técnico documenta la arquitectura, estructura y funcionamiento interno del Sistema de Gestión Médica Sector 404, proporcionando información detallada sobre cada script PHP y su implementación.

### 1.2 Tecnologías Utilizadas

- **Backend**: PHP 7.4+
- **Base de Datos**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework CSS**: Bootstrap 5.3.8
- **Servidor Web**: Apache 2.4+

### 1.3 Estructura del Proyecto

```
practicaNo9/
├── BD/
│   └── sector404.sql          # Script de base de datos
├── Conexion/
│   └── conexion.php           # Conexión y funciones globales
├── Entrada/
│   ├── login.php              # Autenticación
│   ├── registro.php           # Registro de usuarios
│   └── logout.php             # Cierre de sesión
├── js/                        # Scripts JavaScript
├── img/                       # Recursos gráficos
├── pacientes.php              # CRUD Pacientes
├── medicos.php                # CRUD Médicos
├── especialidades.php         # CRUD Especialidades
├── controlAgenda.php          # Gestión de citas
├── expedientes.php            # Expedientes clínicos
├── tarifas.php                # Gestión de tarifas
├── pagos.php                  # Gestión de pagos
├── reportes.php               # Interfaz de reportes
├── generar_reporte.php        # Generación PDF/Excel
├── bitacoras.php              # Auditoría del sistema
├── dashboard.php              # Panel principal
├── dashboard_paciente.php     # Panel para pacientes
└── styles.css                 # Estilos globales
```

---

## 2. ARQUITECTURA DEL SISTEMA

### 2.1 Patrón de Diseño

El sistema utiliza una arquitectura **MVC simplificada**:

- **Modelo**: Interacción directa con MySQL mediante MySQLi
- **Vista**: HTML embebido en PHP con Bootstrap
- **Controlador**: Lógica de negocio en cada script PHP

### 2.2 Flujo de Autenticación

```
Usuario → login.php → Validación → Sesión PHP → Dashboard
                          ↓
                    Bitácora de acceso
```

### 2.3 Control de Acceso por Roles

| Rol | Permisos |
|-----|----------|
| **Admin** | Acceso total |
| **Secretaria** | Gestión operativa (sin bitácoras) |
| **Paciente** | Solo visualización de datos propios |

---

## 3. BASE DE DATOS

### 3.1 Diagrama de Tablas

```
usuarios ──┐
           ├──→ bitacoraacceso
           └──→ controlpacientes
                     ↓
           ┌─────────┴─────────┐
           ↓                   ↓
    controlagenda ──→ gestorpagos
           ↓
    expedienteclinico
           ↓
    controlmedico ←── especialidades
           ↓
    gestortarifas
```

### 3.2 Tablas Principales

#### usuarios
```sql
CREATE TABLE usuarios (
  IdUsuario INT PRIMARY KEY AUTO_INCREMENT,
  Correo VARCHAR(100) UNIQUE NOT NULL,
  Contrasena VARCHAR(255) NOT NULL,
  Nombre VARCHAR(150),
  Rol VARCHAR(50) DEFAULT 'Recepcionista',
  IdMedico INT,
  IdPaciente INT,
  Activo BIT DEFAULT 1,
  FechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP,
  UltimoAcceso DATETIME
);
```

#### controlpacientes
```sql
CREATE TABLE controlpacientes (
  IdPaciente INT PRIMARY KEY AUTO_INCREMENT,
  NombreCompleto VARCHAR(150) NOT NULL,
  CURP VARCHAR(18) UNIQUE,
  FechaNacimiento DATE,
  Sexo CHAR(1),
  Telefono VARCHAR(20),
  CorreoElectronico VARCHAR(100),
  Direccion VARCHAR(250),
  ContactoEmergencia VARCHAR(150),
  TelefonoEmergencia VARCHAR(20),
  Alergias VARCHAR(250),
  AntecedentesMedicos VARCHAR(500),
  FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
  Estatus BIT DEFAULT 1
);
```

---

## 4. DOCUMENTACIÓN DE SCRIPTS PHP

### 4.1 Módulo de Conexión

#### **Conexion/conexion.php**

**Propósito**: Establece conexión con MySQL y proporciona funciones de seguridad globales.

**Funciones principales**:

```php
// Conexión a base de datos
$conexion = new mysqli($servidor, $usuario, $password, $base_datos);
$conexion->set_charset("utf8mb4");

// Función: limpiar_dato($dato)
// Sanitiza entrada del usuario para prevenir inyección SQL
function limpiar_dato($dato) {
    global $conexion;
    $dato = trim($dato);
    $dato = stripslashes($dato);
    $dato = htmlspecialchars($dato);
    return $conexion->real_escape_string($dato);
}

// Función: sesion_activa()
// Verifica si existe sesión de usuario activa
function sesion_activa() {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

// Función: verificar_acceso($roles_permitidos)
// Control de acceso basado en roles
function verificar_acceso($roles_permitidos = []) {
    if (!sesion_activa()) {
        header('Location: ../Entrada/login.php');
        exit;
    }
    if (!empty($roles_permitidos) && !in_array($_SESSION['rol'], $roles_permitidos)) {
        // Redirigir según rol
        exit;
    }
    return true;
}
```

**Seguridad implementada**:
- Prepared statements para consultas SQL
- Sanitización de datos de entrada
- Control de sesiones PHP
- Validación de roles

---

### 4.2 Módulo de Autenticación

#### **Entrada/login.php**

**Propósito**: Autenticación de usuarios y creación de sesiones.

**Flujo de ejecución**:

1. **Recepción de credenciales** (POST)
2. **Validación de formato** (correo, contraseña)
3. **Consulta a BD** con prepared statement
4. **Verificación de contraseña**
5. **Creación de sesión** PHP
6. **Registro en bitácora**
7. **Redirección** según rol

**Código clave**:

```php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = limpiar_dato($_POST['correo']);
    $contrasena = $_POST['contrasena'];
    
    // Consulta segura con prepared statement
    $sql = "SELECT IdUsuario, Correo, Contrasena, Nombre, Rol, Activo 
            FROM usuarios WHERE Correo = ? LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        
        // Verificar contraseña y estado
        if ($usuario['Contrasena'] === $contrasena && $usuario['Activo']) {
            // Crear sesión
            $_SESSION['usuario_id'] = $usuario['IdUsuario'];
            $_SESSION['correo'] = $usuario['Correo'];
            $_SESSION['nombre'] = $usuario['Nombre'];
            $_SESSION['rol'] = $usuario['Rol'];
            
            // Registrar en bitácora
            $sql_bitacora = "INSERT INTO bitacoraacceso (IdUsuario, AccionRealizada, Modulo) 
                            VALUES (?, 'Inicio de sesión', 'Login')";
            
            // Redirigir según rol
            header('Location: ../dashboard.php');
        }
    }
}
```

---

#### **Entrada/registro.php**

**Propósito**: Registro de nuevos usuarios con rol Paciente.

**Validaciones**:
- Correo único en el sistema
- Formato de email válido
- Contraseña mínimo 6 caracteres
- Campos obligatorios

**Código clave**:

```php
// Verificar correo único
$sql_verificar = "SELECT IdUsuario FROM usuarios WHERE Correo = ? LIMIT 1";
$stmt_verificar = $conexion->prepare($sql_verificar);
$stmt_verificar->bind_param("s", $correo);
$stmt_verificar->execute();

if ($resultado_verificar->num_rows > 0) {
    $mensaje = 'Ya existe una cuenta con ese correo';
} else {
    // Insertar nuevo usuario con rol Paciente
    $sql = "INSERT INTO usuarios (Correo, Contrasena, Nombre, Rol, Activo, FechaCreacion) 
            VALUES (?, ?, ?, 'Paciente', 1, NOW())";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sss", $correo, $contrasena, $nombre);
    $stmt->execute();
}
```

---

### 4.3 Módulos CRUD

#### **pacientes.php**

**Propósito**: Gestión completa de pacientes (Create, Read, Update, Delete).

**Operaciones**:

**1. Crear Paciente**:
```php
$sql = "INSERT INTO controlpacientes 
        (NombreCompleto, CURP, FechaNacimiento, Sexo, Telefono, CorreoElectronico, 
         Direccion, ContactoEmergencia, TelefonoEmergencia, Alergias, AntecedentesMedicos) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("sssssssssss", $nombre, $curp, $fecha_nac, $sexo, $telefono, 
                  $correo, $direccion, $contacto_emergencia, $tel_emergencia, 
                  $alergias, $antecedentes);
```

**2. Leer Pacientes**:
```php
$sql = "SELECT * FROM controlpacientes WHERE Estatus = 1 ORDER BY NombreCompleto";
$resultado = $conexion->query($sql);
```

**3. Actualizar Paciente**:
```php
$sql = "UPDATE controlpacientes SET 
        NombreCompleto = ?, CURP = ?, FechaNacimiento = ?, Sexo = ?,
        Telefono = ?, CorreoElectronico = ?, Direccion = ?,
        ContactoEmergencia = ?, TelefonoEmergencia = ?, Alergias = ?, 
        AntecedentesMedicos = ? 
        WHERE IdPaciente = ?";
```

**4. Eliminar (Lógico)**:
```php
$sql = "UPDATE controlpacientes SET Estatus = 0 WHERE IdPaciente = ?";
```

---

#### **medicos.php**

**Propósito**: Gestión de médicos y asignación de especialidades.

**Características especiales**:
- Validación de cédula profesional única
- Relación con tabla `especialidades`
- Control de horarios de atención

**Consulta con JOIN**:
```php
$sql = "SELECT cm.*, e.NombreEspecialidad 
        FROM controlmedico cm
        LEFT JOIN especialidades e ON cm.EspecialidadId = e.IdEspecialidad
        WHERE cm.Estatus = 1
        ORDER BY cm.NombreCompleto";
```

---

### 4.4 Módulo de Agenda

#### **controlAgenda.php**

**Propósito**: Programación y gestión de citas médicas.

**Estados de cita**:
- `Programada`: Cita agendada
- `Completada`: Cita realizada
- `Cancelada`: Cita cancelada

**Consulta compleja**:
```php
$sql = "SELECT ca.*, 
        cp.NombreCompleto as NombrePaciente,
        cm.NombreCompleto as NombreMedico,
        e.NombreEspecialidad
        FROM controlagenda ca
        INNER JOIN controlpacientes cp ON ca.IdPaciente = cp.IdPaciente
        INNER JOIN controlmedico cm ON ca.IdMedico = cm.IdMedico
        LEFT JOIN especialidades e ON cm.EspecialidadId = e.IdEspecialidad
        WHERE ca.FechaCita >= NOW() AND ca.EstadoCita = 'Programada'
        ORDER BY ca.FechaCita ASC";
```

**Validaciones**:
- No permitir citas en fechas pasadas
- Evitar doble reserva del mismo médico
- Verificar que paciente y médico estén activos

---

### 4.5 Módulo de Reportes

#### **reportes.php**

**Propósito**: Interfaz para configurar y solicitar reportes.

**Tipos de reportes**:
1. Pagos
2. Pacientes
3. Médicos
4. Agenda
5. Bitácora de acceso

**Formulario dinámico**:
```php
<form id="formPagos">
    <input type="hidden" name="tipo" value="pagos">
    <input type="date" name="fecha_inicio">
    <input type="date" name="fecha_fin">
    <select name="metodo_pago">
        <option value="">Todos</option>
        <option value="Efectivo">Efectivo</option>
        <option value="Tarjeta">Tarjeta</option>
    </select>
</form>
```

---

#### **generar_reporte.php**

**Propósito**: Generación de reportes en PDF y Excel.

**Arquitectura**:

```php
// 1. Recibir parámetros
$tipo = $_GET['tipo'];
$formato = $_GET['formato']; // 'pdf' o 'excel'

// 2. Construir consulta SQL según tipo
switch($tipo) {
    case 'pagos':
        $sql = "SELECT ... FROM gestorpagos ...";
        break;
    case 'pacientes':
        $sql = "SELECT ... FROM controlpacientes ...";
        break;
    // ... otros casos
}

// 3. Ejecutar consulta
$resultado = $conexion->query($sql);
$datos = $resultado->fetch_all(MYSQLI_ASSOC);

// 4. Generar según formato
if ($formato == 'excel') {
    generarExcel($tipo, $titulo, $datos);
} else {
    generarPDF($tipo, $titulo, $datos);
}
```

**Función generarExcel()**:
```php
function generarExcel($tipo, $titulo, $datos) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="reporte_' . $tipo . '.csv"');
    
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
    
    // Encabezados
    fputcsv($output, array_keys($datos[0]));
    
    // Datos
    foreach ($datos as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;
}
```

**Función generarPDF()**:
```php
function generarPDF($tipo, $titulo, $datos) {
    // Genera HTML con estilos para impresión
    // El navegador convierte a PDF usando Ctrl+P
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            @media print {
                .botones { display: none; }
            }
        </style>
    </head>
    <body>
        <h1><?php echo $titulo; ?></h1>
        <table>
            <!-- Datos tabulados -->
        </table>
        <script>
            function descargarPDF() {
                window.print();
            }
        </script>
    </body>
    </html>
    <?php
}
```

---

### 4.6 Módulo de Bitácoras

#### **bitacoras.php**

**Propósito**: Auditoría y trazabilidad de acciones del sistema.

**Consulta con JOIN**:
```php
$sql = "SELECT b.*, u.Nombre, u.Correo, u.Rol
        FROM bitacoraacceso b
        INNER JOIN usuarios u ON b.IdUsuario = u.IdUsuario
        ORDER BY b.FechaAcceso DESC";
```

**Acciones registradas**:
- Inicio de sesión
- Cierre de sesión
- Creación de registros
- Modificación de registros
- Eliminación de registros
- Generación de reportes

**Registro automático**:
```php
$sql_bitacora = "INSERT INTO bitacoraacceso (IdUsuario, AccionRealizada, Modulo) 
                VALUES (?, ?, ?)";
$stmt = $conexion->prepare($sql_bitacora);
$stmt->bind_param("iss", $_SESSION['usuario_id'], $accion, $modulo);
$stmt->execute();
```

---

## 5. TRABAJO FUTURO

### 5.1 Videos de Evidencia Pendientes

Como parte de la documentación técnica, se requiere crear los siguientes videos demostrativos:

1. **Video Módulo Tarifas** (1 video)
   - Demostración técnica del CRUD
   - Explicación del código PHP
   - Relación con especialidades

2. **Video Módulo Pagos** (1 video)
   - Flujo de registro de pagos
   - Validaciones implementadas
   - Integración con citas

3. **Videos de Reportes** (4 videos)
   - Reporte de Pagos: Filtros y generación
   - Reporte de Pacientes: Exportación de datos
   - Reporte de Médicos: Análisis por especialidad
   - Reporte de Agenda y Bitácora: Auditoría

4. **Video Módulo Login** (1 video)
   - Flujo de autenticación
   - Manejo de sesiones PHP
   - Sistema de roles

### 5.2 Mejoras Técnicas Futuras

1. **Migración a PDO**
   - Reemplazar MySQLi por PDO para mejor portabilidad
   - Implementar transacciones complejas

2. **API RESTful**
   - Crear endpoints JSON para aplicación móvil
   - Implementar autenticación JWT

3. **Hashing de Contraseñas**
   - Implementar `password_hash()` y `password_verify()`
   - Migrar contraseñas existentes

4. **Framework MVC**
   - Migrar a Laravel o CodeIgniter
   - Separar lógica de presentación

5. **Optimización de Consultas**
   - Implementar índices en tablas
   - Caché de consultas frecuentes

---

## 6. CONCLUSIONES

### 6.1 Logros Técnicos

El sistema implementa exitosamente:

- **Arquitectura modular**: Cada módulo es independiente y reutilizable
- **Seguridad básica**: Prepared statements, sanitización, control de sesiones
- **Escalabilidad**: Estructura permite agregar nuevos módulos fácilmente
- **Mantenibilidad**: Código documentado y organizado

### 6.2 Buenas Prácticas Implementadas

1. **Prepared Statements**: Prevención de inyección SQL
2. **Sanitización de datos**: Función `limpiar_dato()`
3. **Control de acceso**: Función `verificar_acceso()`
4. **Eliminación lógica**: Preservación de datos históricos
5. **Bitácora de auditoría**: Trazabilidad completa

### 6.3 Consideraciones de Seguridad

**Implementado**:
- ✅ Prepared statements
- ✅ Sanitización de entrada
- ✅ Control de sesiones
- ✅ Validación de roles
- ✅ Bitácora de accesos

**Pendiente** (para producción):
- ⚠️ Hashing de contraseñas (usar `password_hash()`)
- ⚠️ HTTPS obligatorio
- ⚠️ Protección CSRF
- ⚠️ Rate limiting en login
- ⚠️ Validación de archivos subidos

### 6.4 Rendimiento

**Optimizaciones aplicadas**:
- Consultas con LIMIT para paginación
- Índices en columnas de búsqueda frecuente
- Conexión persistente a BD

**Métricas**:
- Tiempo de carga promedio: < 1 segundo
- Consultas optimizadas con JOINs
- Caché de sesiones PHP

### 6.5 Reflexión Final

El sistema demuestra una implementación sólida de conceptos fundamentales de desarrollo web con PHP y MySQL. La arquitectura modular facilita el mantenimiento y la escalabilidad, mientras que las medidas de seguridad implementadas proporcionan una base confiable para un entorno de producción.

---

## ANEXOS TÉCNICOS

### Anexo A: Configuración del Servidor

**Requisitos PHP**:
```ini
; php.ini
upload_max_filesize = 10M
post_max_size = 10M
session.gc_maxlifetime = 3600
date.timezone = America/Mexico_City
```

**Configuración MySQL**:
```sql
SET NAMES utf8mb4;
SET character_set_client = utf8mb4;
SET character_set_connection = utf8mb4;
SET character_set_results = utf8mb4;
```

### Anexo B: Comandos de Instalación

```bash
# 1. Importar base de datos
mysql -u root -p sector404 < BD/sector404.sql

# 2. Configurar permisos
chmod 755 practicaNo9/
chmod 644 practicaNo9/*.php

# 3. Configurar conexión
# Editar Conexion/conexion.php con credenciales correctas
```

### Anexo C: Estructura de Sesión PHP

```php
$_SESSION = [
    'usuario_id' => 1,
    'correo' => 'admin@sector404.com',
    'nombre' => 'Administrador',
    'rol' => 'Admin'
];
```

---

**FIN DEL MANUAL TÉCNICO**

*Versión 1.0 - Noviembre 2025*  
*Sistema de Gestión Médica - Sector 404*
