# Sector 404 - Sistema de Gestión Médica

**[Acceder a la Aplicación](http://134.209.49.200/2430117-AW/practicaNo9/Entrada/login.php)**

---

## Descripción

Sistema web desarrollado en PHP y MySQL para administrar consultorios médicos. Permite gestionar médicos, especialidades, servicios, pagos y control de pacientes.

## Características

- Gestión de médicos (crear, editar, eliminar)
- Control de especialidades médicas
- Administración de servicios y tarifas
- Registro de pagos
- Autenticación con roles (Admin, Secretaria)
- Interfaz responsiva

## Tecnologías

- PHP 8.x
- MySQL / MariaDB
- Bootstrap 5
- JavaScript

## Credenciales de Prueba

```
Correo: admin@gmail.com
Contraseña: (solicitar al administrador)
Rol: Admin
```

## Instalación

1. Copiar archivos al servidor web
2. Crear base de datos `sector404` en MySQL
3. Importar archivo SQL
4. Configurar `Conexion/conexion.php` con credenciales
5. Acceder a `login.php`

## Módulos Funcionales

### Médicos
Registrar, editar y eliminar médicos con especialidad asignada.

### Especialidades
Gestionar las especialidades disponibles.

### Servicios
Administrar servicios y tarifas.

### Pagos
Registro y seguimiento de pagos.

## Estructura

```
practicaNo9/
├── Conexion/
│   └── conexion.php
├── Entrada/
│   ├── login.php
│   ├── registro.php
│   └── logout.php
├── js/
│   ├── dashboard.js
│   └── medicos.js
├── dashboard.php
├── medicos.php
├── especialidades.php
├── servicios.php
├── pagos.php
└── styles.css
```

## Notas

- Contraseñas almacenadas en texto plano (solo para educación)
- Borrado lógico de registros (no se eliminan de la BD)
- Control de acceso por roles
- Prepared statements para seguridad

## Autor

Proyecto educativo - Sector 404
