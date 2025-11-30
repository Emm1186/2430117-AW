# Manual de Usuario
## Sistema de Gestión Médica - Sector 404

---

<div style="page-break-after: always;"></div>

## PORTADA

**Sistema de Gestión Médica**  
**Sector 404**

**Manual de Usuario**  
Versión 1.0

**Fecha:** Noviembre 2025

**Desarrollado por:** Equipo Sector 404

**Institución:** 2430117-AW

---

<div style="page-break-after: always;"></div>

## ÍNDICE

1. [Introducción](#introducción)
   - 1.1 Propósito del Sistema
   - 1.2 Alcance
   - 1.3 Usuarios del Sistema

2. [Desarrollo - Módulos del Sistema](#desarrollo---módulos-del-sistema)
   - 2.1 Login de Usuarios
   - 2.2 Registro de Usuarios
   - 2.3 Dashboard Principal
   - 2.4 Control de Pacientes
   - 2.5 Control de Médicos
   - 2.6 Especialidades Médicas
   - 2.7 Control de Agenda
   - 2.8 Expedientes Clínicos
   - 2.9 Gestor de Tarifas
   - 2.10 Gestor de Pagos
   - 2.11 Reportes del Sistema
   - 2.12 Bitácoras de Acceso

3. [Trabajo Futuro](#trabajo-futuro)
   - 3.1 Videos de Evidencia Pendientes

4. [Conclusiones](#conclusiones)

---

<div style="page-break-after: always;"></div>

## 1. INTRODUCCIÓN

### 1.1 Propósito del Sistema

El **Sistema de Gestión Médica Sector 404** es una aplicación web diseñada para facilitar la administración integral de una clínica u hospital. El sistema permite gestionar pacientes, médicos, citas médicas, expedientes clínicos, pagos y generar reportes detallados de todas las operaciones.

Este manual tiene como objetivo guiar a los usuarios en el uso correcto de cada uno de los módulos del sistema, explicando paso a paso las funcionalidades disponibles.

### 1.2 Alcance

El sistema cubre las siguientes áreas:

- **Gestión de Usuarios**: Control de acceso con diferentes roles (Super Admin, Secretaria, Paciente)
- **Gestión de Pacientes**: Registro completo de información personal y médica
- **Gestión de Médicos**: Control de profesionales de la salud y sus especialidades
- **Agenda Médica**: Programación y seguimiento de citas
- **Expedientes Clínicos**: Historial médico detallado de cada paciente
- **Gestión Financiera**: Control de tarifas y pagos
- **Reportes**: Generación de informes en PDF y Excel
- **Auditoría**: Bitácora de accesos y acciones del sistema

### 1.3 Usuarios del Sistema

El sistema está diseñado para tres tipos de usuarios:

| Rol | Descripción | Permisos |
|-----|-------------|----------|
| **Super Admin** | Administrador del sistema | Acceso total a todos los módulos |
| **Secretaria** | Personal administrativo | Gestión de pacientes, citas, pagos y reportes |
| **Paciente** | Usuario final | Visualización de su información y citas |

---

<div style="page-break-after: always;"></div>

## 2. DESARROLLO - MÓDULOS DEL SISTEMA

### 2.1 Login de Usuarios

**Descripción:** Módulo de autenticación que permite el acceso seguro al sistema.

**Acceso:** `http://servidor/practicaNo9/Entrada/login.php`

#### Funcionalidades:

1. **Inicio de Sesión**
   - Ingrese su correo electrónico registrado
   - Ingrese su contraseña
   - Haga clic en "Iniciar sesión"
   - El sistema validará sus credenciales y lo redirigirá al dashboard correspondiente

2. **Registro de Nueva Cuenta**
   - Si no tiene cuenta, haga clic en "Crear cuenta"
   - Será redirigido al módulo de registro

3. **Seguridad**
   - Las contraseñas están protegidas
   - Se registra cada inicio de sesión en la bitácora
   - Sesiones con tiempo de expiración automático

#### Capturas de Pantalla:

```
[Pantalla de Login]
- Campo: Correo electrónico
- Campo: Contraseña
- Botón: Iniciar sesión
- Enlace: ¿No tienes cuenta? Crear cuenta
```

#### Mensajes del Sistema:

- ✅ "Inicio de sesión exitoso"
- ❌ "Credenciales incorrectas"
- ❌ "Usuario inactivo"

---

### 2.2 Registro de Usuarios

**Descripción:** Permite a nuevos usuarios crear una cuenta en el sistema con rol de Paciente.

**Acceso:** `http://servidor/practicaNo9/Entrada/registro.php`

#### Funcionalidades:

1. **Crear Nueva Cuenta**
   - **Nombre completo**: Ingrese su nombre
   - **Correo electrónico**: Debe ser único en el sistema
   - **Contraseña**: Mínimo 6 caracteres
   - Haga clic en "Registrar"

2. **Validaciones Automáticas**
   - Verifica que el correo no esté registrado
   - Valida formato de correo electrónico
   - Verifica longitud mínima de contraseña
   - Todos los campos son obligatorios

3. **Proceso Post-Registro**
   - La cuenta se crea con rol "Paciente"
   - El usuario es redirigido automáticamente al login
   - Puede iniciar sesión inmediatamente

#### Notas Importantes:

> [!IMPORTANT]
> - Los usuarios registrados públicamente siempre tienen rol "Paciente"
> - Los roles de Admin y Secretaria son asignados por el administrador del sistema
> - El correo electrónico no puede ser modificado después del registro

---

<div style="page-break-after: always;"></div>

### 2.3 Dashboard Principal

**Descripción:** Panel de control central que muestra estadísticas y accesos rápidos.

**Acceso:** `http://servidor/practicaNo9/dashboard.php`

#### Funcionalidades:

1. **Estadísticas en Tiempo Real**
   - **Total de Pacientes**: Contador de pacientes activos
   - **Total de Médicos**: Contador de médicos activos
   - **Citas de Hoy**: Citas programadas para el día actual
   - **Especialidades**: Total de especialidades médicas disponibles

2. **Próximas Citas**
   - Tabla con las 5 citas más próximas
   - Información mostrada:
     - Nombre del paciente
     - Médico asignado
     - Fecha y hora de la cita
     - Motivo de consulta

3. **Acceso Rápido**
   - Botones de acceso directo a:
     - 📅 Nueva Cita
     - 👤 Nuevo Paciente
     - 👨‍⚕️ Nuevo Médico (solo Admin/Secretaria)
     - 💳 Registrar Pago
     - 📊 Ver Reportes

4. **Barra de Navegación Lateral**
   - Menú con todos los módulos del sistema
   - Indicador visual del módulo activo
   - Organizado por categorías

#### Elementos de la Interfaz:

```
Header:
- Logo: 🏥 Sector 404
- Usuario actual y rol
- Botón: Cerrar sesión

Sidebar:
- 📋 Menú
  - 🏠 Inicio
  - 👥 Control de pacientes
  - 📅 Control de agenda
  - 👨‍⚕️ Control de médicos
  - 📋 Expedientes médicos
  - 🩺 Especialidades médicas
  - 💰 Gestor de tarifas
  - 💳 Pagos
  - 📊 Reportes
- ⚙️ Administración
  - 📝 Bitácoras
```

---

<div style="page-break-after: always;"></div>

### 2.4 Control de Pacientes

**Descripción:** Módulo completo para la gestión de pacientes del sistema.

**Acceso:** `http://servidor/practicaNo9/pacientes.php`

**Permisos:** Admin, Secretaria

#### Funcionalidades:

1. **Listar Pacientes**
   - Tabla con todos los pacientes registrados
   - Información mostrada:
     - Nombre completo
     - CURP
     - Fecha de nacimiento
     - Sexo
     - Teléfono
     - Correo electrónico
     - Estado (Activo/Inactivo)
   - Acciones disponibles: Editar, Eliminar

2. **Registrar Nuevo Paciente**
   - Haga clic en el botón "➕ Nuevo Paciente"
   - Complete el formulario con los siguientes datos:
     - **Nombre completo** (obligatorio)
     - **CURP** (único, 18 caracteres)
     - **Fecha de nacimiento**
     - **Sexo** (M/F)
     - **Teléfono**
     - **Correo electrónico**
     - **Dirección**
     - **Contacto de emergencia**
     - **Teléfono de emergencia**
     - **Alergias conocidas**
     - **Antecedentes médicos**
   - Haga clic en "Guardar"

3. **Editar Paciente**
   - Haga clic en el botón "✏️ Editar" del paciente deseado
   - Se abrirá un modal con la información actual
   - Modifique los campos necesarios
   - Haga clic en "Guardar cambios"

4. **Eliminar Paciente**
   - Haga clic en el botón "🗑️ Eliminar"
   - Confirme la acción en el mensaje de alerta
   - El paciente se marca como inactivo (eliminación lógica)

5. **Búsqueda y Filtros**
   - Barra de búsqueda en tiempo real
   - Filtro por estado (Activo/Inactivo)
   - Ordenamiento por columnas

#### Validaciones:

- CURP debe ser único y tener 18 caracteres
- Correo electrónico debe tener formato válido
- Fecha de nacimiento no puede ser futura
- Teléfonos deben tener formato numérico

---

<div style="page-break-after: always;"></div>

### 2.5 Control de Médicos

**Descripción:** Gestión completa del personal médico de la clínica.

**Acceso:** `http://servidor/practicaNo9/medicos.php`

**Permisos:** Admin, Secretaria

#### Funcionalidades:

1. **Listar Médicos**
   - Tabla con todos los médicos registrados
   - Información mostrada:
     - Nombre completo
     - Cédula profesional
     - Especialidad
     - Teléfono
     - Correo electrónico
     - Horario de atención
     - Estado (Activo/Inactivo)
   - Acciones: Editar, Eliminar

2. **Registrar Nuevo Médico**
   - Haga clic en "➕ Nuevo Médico"
   - Complete el formulario:
     - **Nombre completo** (obligatorio)
     - **Cédula profesional** (única, obligatoria)
     - **Especialidad** (seleccionar de lista)
     - **Teléfono**
     - **Correo electrónico**
     - **Horario de atención** (ej: "Lun-Vie 9:00-17:00")
   - Haga clic en "Guardar"

3. **Editar Médico**
   - Haga clic en "✏️ Editar"
   - Modifique la información en el modal
   - Guarde los cambios

4. **Eliminar Médico**
   - Haga clic en "🗑️ Eliminar"
   - Confirme la acción
   - El médico se marca como inactivo

5. **Asignación de Especialidad**
   - Cada médico debe tener una especialidad asignada
   - Las especialidades se gestionan en el módulo correspondiente

#### Validaciones:

- Cédula profesional debe ser única
- Especialidad debe existir en el catálogo
- Correo electrónico debe ser válido
- No se puede eliminar un médico con citas programadas

---

<div style="page-break-after: always;"></div>

### 2.6 Especialidades Médicas

**Descripción:** Catálogo de especialidades médicas disponibles en la clínica.

**Acceso:** `http://servidor/practicaNo9/especialidades.php`

**Permisos:** Admin, Secretaria

#### Funcionalidades:

1. **Listar Especialidades**
   - Tabla con todas las especialidades
   - Información mostrada:
     - Nombre de la especialidad
     - Descripción
   - Acciones: Editar, Eliminar

2. **Agregar Nueva Especialidad**
   - Haga clic en "➕ Nueva Especialidad"
   - Complete el formulario:
     - **Nombre de la especialidad** (obligatorio, único)
     - **Descripción** (opcional)
   - Haga clic en "Guardar"

3. **Editar Especialidad**
   - Haga clic en "✏️ Editar"
   - Modifique el nombre o descripción
   - Guarde los cambios

4. **Eliminar Especialidad**
   - Haga clic en "🗑️ Eliminar"
   - Confirme la acción
   - **Nota:** No se puede eliminar una especialidad asignada a médicos activos

#### Especialidades Predeterminadas:

El sistema incluye las siguientes especialidades por defecto:

1. **Medicina General** - Atención médica general y consultas básicas
2. **Cardiología** - Especialista en enfermedades del corazón
3. **Pediatría** - Atención médica infantil
4. **Dermatología** - Tratamiento de enfermedades de la piel
5. **Traumatología** - Tratamiento de lesiones musculares y óseas

---

<div style="page-break-after: always;"></div>

### 2.7 Control de Agenda

**Descripción:** Gestión de citas médicas con calendario interactivo.

**Acceso:** `http://servidor/practicaNo9/controlAgenda.php`

**Permisos:** Admin, Secretaria, Paciente (solo visualización)

#### Funcionalidades:

1. **Visualizar Agenda**
   - Calendario mensual con todas las citas
   - Vista de lista con próximas citas
   - Código de colores por estado:
     - 🟢 Verde: Programada
     - 🔵 Azul: Completada
     - 🔴 Rojo: Cancelada

2. **Agendar Nueva Cita**
   - Haga clic en "➕ Nueva Cita"
   - Complete el formulario:
     - **Paciente** (seleccionar de lista)
     - **Médico** (seleccionar de lista)
     - **Fecha y hora de la cita**
     - **Motivo de consulta**
     - **Observaciones** (opcional)
   - Haga clic en "Guardar"

3. **Editar Cita**
   - Haga clic en la cita en el calendario o en "✏️ Editar"
   - Modifique los datos necesarios
   - Guarde los cambios

4. **Cancelar Cita**
   - Haga clic en "❌ Cancelar"
   - Confirme la cancelación
   - La cita cambia a estado "Cancelada"

5. **Marcar como Completada**
   - Haga clic en "✅ Completar"
   - La cita cambia a estado "Completada"

6. **Filtros de Búsqueda**
   - Por paciente
   - Por médico
   - Por fecha
   - Por estado de cita

#### Estados de Cita:

| Estado | Descripción |
|--------|-------------|
| **Programada** | Cita agendada pendiente de realizarse |
| **Completada** | Cita realizada exitosamente |
| **Cancelada** | Cita cancelada por el paciente o médico |

#### Validaciones:

- No se pueden agendar citas en fechas pasadas
- No se pueden agendar dos citas al mismo médico en el mismo horario
- El paciente y médico deben estar activos

---

<div style="page-break-after: always;"></div>

### 2.8 Expedientes Clínicos

**Descripción:** Historial médico completo de cada paciente.

**Acceso:** `http://servidor/practicaNo9/expedientes.php`

**Permisos:** Admin, Secretaria, Médicos

#### Funcionalidades:

1. **Listar Expedientes**
   - Tabla con todos los expedientes
   - Información mostrada:
     - Paciente
     - Médico que atendió
     - Fecha de consulta
     - Diagnóstico
     - Próxima cita
   - Acciones: Ver detalle, Editar, Eliminar

2. **Crear Nuevo Expediente**
   - Haga clic en "➕ Nuevo Expediente"
   - Complete el formulario:
     - **Paciente** (seleccionar)
     - **Médico** (seleccionar)
     - **Fecha de consulta**
     - **Síntomas** (descripción detallada)
     - **Diagnóstico**
     - **Tratamiento** (indicaciones médicas)
     - **Receta médica** (medicamentos prescritos)
     - **Notas adicionales**
     - **Próxima cita** (fecha sugerida)
   - Haga clic en "Guardar"

3. **Ver Detalle de Expediente**
   - Haga clic en "👁️ Ver"
   - Se muestra toda la información del expediente
   - Historial completo del paciente

4. **Editar Expediente**
   - Haga clic en "✏️ Editar"
   - Modifique la información necesaria
   - Guarde los cambios

5. **Buscar Expedientes**
   - Por nombre de paciente
   - Por médico
   - Por rango de fechas
   - Por diagnóstico

#### Información del Expediente:

**Datos del Paciente:**
- Nombre completo
- Edad
- Alergias conocidas
- Antecedentes médicos

**Datos de la Consulta:**
- Fecha y hora
- Médico que atendió
- Síntomas presentados
- Signos vitales (si aplica)

**Diagnóstico y Tratamiento:**
- Diagnóstico médico
- Tratamiento prescrito
- Receta médica
- Indicaciones especiales

**Seguimiento:**
- Próxima cita sugerida
- Notas adicionales
- Observaciones

---

<div style="page-break-after: always;"></div>

### 2.9 Gestor de Tarifas

**Descripción:** Catálogo de servicios médicos y sus costos.

**Acceso:** `http://servidor/practicaNo9/tarifas.php`

**Permisos:** Admin, Secretaria

#### Funcionalidades:

1. **Listar Tarifas**
   - Tabla con todos los servicios
   - Información mostrada:
     - Descripción del servicio
     - Costo base
     - Especialidad asociada
     - Estado (Activo/Inactivo)
   - Acciones: Editar, Eliminar

2. **Agregar Nueva Tarifa**
   - Haga clic en "➕ Nueva Tarifa"
   - Complete el formulario:
     - **Descripción del servicio** (obligatorio)
     - **Costo base** (en pesos, obligatorio)
     - **Especialidad** (opcional, seleccionar de lista)
   - Haga clic en "Guardar"

3. **Editar Tarifa**
   - Haga clic en "✏️ Editar"
   - Modifique la descripción o costo
   - Guarde los cambios

4. **Eliminar Tarifa**
   - Haga clic en "🗑️ Eliminar"
   - Confirme la acción
   - La tarifa se marca como inactiva

5. **Filtros**
   - Por especialidad
   - Por rango de precio
   - Por estado (Activo/Inactivo)

#### Ejemplos de Tarifas:

| Servicio | Costo | Especialidad |
|----------|-------|--------------|
| Consulta General | $350.00 | Medicina General |
| Consulta Especializada | $500.00 | Cardiología |
| Consulta Pediátrica | $400.00 | Pediatría |
| Electrocardiograma | $250.00 | Cardiología |
| Análisis de Laboratorio | $300.00 | General |

#### Validaciones:

- El costo debe ser mayor a 0
- La descripción del servicio debe ser única
- Si se asocia a una especialidad, esta debe existir

---

<div style="page-break-after: always;"></div>

### 2.10 Gestor de Pagos

**Descripción:** Control de pagos realizados por los pacientes.

**Acceso:** `http://servidor/practicaNo9/pagos.php`

**Permisos:** Admin, Secretaria

#### Funcionalidades:

1. **Listar Pagos**
   - Tabla con todos los pagos registrados
   - Información mostrada:
     - Paciente
     - Cita asociada
     - Monto
     - Método de pago
     - Fecha de pago
     - Referencia
     - Estado del pago
   - Acciones: Ver detalle, Editar, Anular

2. **Registrar Nuevo Pago**
   - Haga clic en "➕ Nuevo Pago"
   - Complete el formulario:
     - **Cita** (seleccionar de lista de citas)
     - **Paciente** (se llena automáticamente según la cita)
     - **Monto** (obligatorio)
     - **Método de pago** (Efectivo, Tarjeta, Transferencia)
     - **Referencia** (número de transacción, opcional)
   - Haga clic en "Guardar"

3. **Editar Pago**
   - Haga clic en "✏️ Editar"
   - Modifique el monto, método o referencia
   - Guarde los cambios

4. **Anular Pago**
   - Haga clic en "❌ Anular"
   - Confirme la acción
   - El estado cambia a "Anulado"

5. **Filtros y Búsqueda**
   - Por paciente
   - Por rango de fechas
   - Por método de pago
   - Por estado (Pagado/Anulado)

#### Métodos de Pago:

- **Efectivo**: Pago en efectivo en caja
- **Tarjeta**: Pago con tarjeta de débito o crédito
- **Transferencia**: Transferencia bancaria

#### Estados de Pago:

| Estado | Descripción |
|--------|-------------|
| **Pagado** | Pago registrado y confirmado |
| **Anulado** | Pago cancelado o reembolsado |

#### Validaciones:

- El monto debe ser mayor a 0
- La cita debe existir y estar activa
- No se puede anular un pago sin autorización

---

<div style="page-break-after: always;"></div>

### 2.11 Reportes del Sistema

**Descripción:** Generación de reportes en PDF y Excel de todas las operaciones.

**Acceso:** `http://servidor/practicaNo9/reportes.php`

**Permisos:** Admin, Secretaria

#### Funcionalidades:

El sistema permite generar 5 tipos de reportes diferentes:

#### 1. Reporte de Pagos 💳

**Filtros disponibles:**
- Fecha desde / Fecha hasta
- Método de pago (Efectivo, Tarjeta, Transferencia)
- Estado del pago (Pagado, Anulado)

**Información incluida:**
- ID del pago
- Paciente
- Cita asociada
- Monto
- Método de pago
- Fecha de pago
- Referencia
- Estado

**Formatos:**
- 📄 PDF: Para impresión
- 📊 Excel: Para análisis de datos

---

#### 2. Reporte de Pacientes 👥

**Filtros disponibles:**
- Sexo (Masculino, Femenino)
- Estado (Activo, Inactivo)

**Información incluida:**
- Nombre completo
- CURP
- Fecha de nacimiento
- Edad
- Sexo
- Teléfono
- Correo electrónico
- Fecha de registro
- Estado

**Formatos:**
- 📄 PDF: Para impresión
- 📊 Excel: Para análisis de datos

---

#### 3. Reporte de Médicos 👨‍⚕️

**Filtros disponibles:**
- Especialidad
- Estado (Activo, Inactivo)

**Información incluida:**
- Nombre completo
- Cédula profesional
- Especialidad
- Teléfono
- Correo electrónico
- Horario de atención
- Fecha de ingreso
- Estado

**Formatos:**
- 📄 PDF: Para impresión
- 📊 Excel: Para análisis de datos

---

#### 4. Reporte de Agenda 📅

**Filtros disponibles:**
- Fecha desde / Fecha hasta
- Médico
- Estado de cita (Programada, Completada, Cancelada)

**Información incluida:**
- Paciente
- Médico
- Fecha y hora de la cita
- Motivo de consulta
- Estado de la cita
- Observaciones

**Formatos:**
- 📄 PDF: Para impresión
- 📊 Excel: Para análisis de datos

---

#### 5. Reporte de Bitácora de Acceso 📝

**Filtros disponibles:**
- Fecha desde / Fecha hasta
- Usuario
- Módulo

**Información incluida:**
- Usuario
- Fecha y hora de acceso
- Acción realizada
- Módulo accedido
- Dirección IP (si aplica)

**Formatos:**
- 📄 PDF: Para auditoría
- 📊 Excel: Para análisis de seguridad

---

#### Cómo Generar un Reporte:

1. Seleccione el tipo de reporte deseado
2. Configure los filtros según sus necesidades
3. Haga clic en el botón del formato deseado:
   - **📄 PDF**: Se abrirá en una nueva ventana para imprimir o descargar
   - **📊 Excel**: Se descargará automáticamente un archivo CSV

#### Notas Importantes:

> [!TIP]
> - Los reportes en PDF son ideales para impresión y presentaciones
> - Los reportes en Excel (CSV) son ideales para análisis de datos y gráficas
> - Puede aplicar múltiples filtros simultáneamente
> - Los reportes se generan en tiempo real con los datos actuales

---

<div style="page-break-after: always;"></div>

### 2.12 Bitácoras de Acceso

**Descripción:** Registro de auditoría de todos los accesos y acciones en el sistema.

**Acceso:** `http://servidor/practicaNo9/bitacoras.php`

**Permisos:** Admin

#### Funcionalidades:

1. **Visualizar Bitácoras**
   - Tabla con todos los registros de acceso
   - Información mostrada:
     - Usuario
     - Fecha y hora de acceso
     - Acción realizada
     - Módulo accedido

2. **Filtros de Búsqueda**
   - Por usuario
   - Por rango de fechas
   - Por módulo
   - Por tipo de acción

3. **Acciones Registradas**
   - Inicio de sesión
   - Cierre de sesión
   - Creación de registros
   - Modificación de registros
   - Eliminación de registros
   - Generación de reportes

#### Información de la Bitácora:

| Campo | Descripción |
|-------|-------------|
| **ID Bitácora** | Identificador único del registro |
| **Usuario** | Usuario que realizó la acción |
| **Fecha/Hora** | Momento exacto de la acción |
| **Acción** | Descripción de la acción realizada |
| **Módulo** | Módulo del sistema donde se realizó |

#### Propósito de la Bitácora:

- **Seguridad**: Detectar accesos no autorizados
- **Auditoría**: Rastrear cambios en el sistema
- **Cumplimiento**: Cumplir con regulaciones de protección de datos
- **Análisis**: Identificar patrones de uso del sistema

> [!IMPORTANT]
> Las bitácoras no pueden ser modificadas ni eliminadas por ningún usuario. Son registros permanentes del sistema.

---

<div style="page-break-after: always;"></div>

## 3. TRABAJO FUTURO

### 3.1 Videos de Evidencia Pendientes

Como parte de la documentación completa del sistema, se requiere la creación de los siguientes videos demostrativos:

#### Videos Requeridos:

1. **Video del Módulo de Tarifas** (1 video)
   - Demostración de cómo agregar, editar y eliminar tarifas
   - Explicación de la asociación con especialidades
   - Gestión del catálogo de servicios
   - Duración estimada: 5-7 minutos

2. **Video del Módulo de Pagos** (1 video)
   - Proceso completo de registro de un pago
   - Diferentes métodos de pago
   - Consulta de historial de pagos
   - Anulación de pagos
   - Duración estimada: 5-7 minutos

3. **Videos de Reportes** (4 videos)
   - **Video 1**: Reporte de Pagos
     - Configuración de filtros
     - Generación en PDF y Excel
     - Interpretación de datos
   
   - **Video 2**: Reporte de Pacientes
     - Filtros por sexo y estado
     - Exportación de datos
   
   - **Video 3**: Reporte de Médicos
     - Filtros por especialidad
     - Análisis de información médica
   
   - **Video 4**: Reporte de Agenda y Bitácora
     - Reporte de citas médicas
     - Reporte de bitácora de acceso
     - Uso para auditoría
   
   - Duración estimada por video: 3-5 minutos

4. **Video del Módulo de Login** (1 video)
   - Proceso de inicio de sesión
   - Registro de nuevos usuarios
   - Recuperación de contraseña (si aplica)
   - Seguridad y roles del sistema
   - Duración estimada: 4-6 minutos

#### Especificaciones Técnicas de los Videos:

- **Formato**: MP4 (H.264)
- **Resolución**: 1920x1080 (Full HD)
- **Audio**: Narración en español con micrófono de calidad
- **Subtítulos**: Incluir subtítulos en español
- **Herramientas sugeridas**: OBS Studio, Camtasia, o similar

#### Contenido de Cada Video:

1. **Introducción** (30 segundos)
   - Presentación del módulo
   - Objetivos del video

2. **Demostración Práctica** (70% del tiempo)
   - Casos de uso reales
   - Paso a paso de cada funcionalidad
   - Mejores prácticas

3. **Consejos y Tips** (20% del tiempo)
   - Errores comunes a evitar
   - Atajos y funcionalidades avanzadas

4. **Conclusión** (30 segundos)
   - Resumen de lo aprendido
   - Recursos adicionales

### 3.2 Mejoras Futuras del Sistema

Además de los videos, se contemplan las siguientes mejoras para versiones futuras:

1. **Módulo de Notificaciones**
   - Recordatorios de citas por correo/SMS
   - Alertas de pagos pendientes
   - Notificaciones de nuevos expedientes

2. **Dashboard para Pacientes**
   - Portal de paciente mejorado
   - Historial médico personal
   - Solicitud de citas en línea

3. **Integración con Laboratorios**
   - Solicitud de estudios de laboratorio
   - Recepción de resultados digitales
   - Integración con expediente clínico

4. **Aplicación Móvil**
   - App para iOS y Android
   - Acceso rápido a citas
   - Notificaciones push

5. **Sistema de Facturación Electrónica**
   - Generación de facturas (CFDI)
   - Integración con SAT
   - Control de ingresos

---

<div style="page-break-after: always;"></div>

## 4. CONCLUSIONES

### 4.1 Logros del Sistema

El **Sistema de Gestión Médica Sector 404** ha cumplido exitosamente con los objetivos planteados:

1. **Gestión Integral**: El sistema cubre todos los aspectos necesarios para la administración de una clínica médica, desde el registro de pacientes hasta la generación de reportes financieros.

2. **Seguridad**: Se implementó un sistema robusto de autenticación y autorización con tres niveles de acceso (Super Admin, Secretaria, Paciente), garantizando que cada usuario solo acceda a la información que le corresponde.

3. **Trazabilidad**: La bitácora de accesos permite un control completo de todas las acciones realizadas en el sistema, cumpliendo con requisitos de auditoría y seguridad.

4. **Reportes Completos**: Los 5 tipos de reportes (Pagos, Pacientes, Médicos, Agenda, Bitácora) en formatos PDF y Excel proporcionan herramientas de análisis y toma de decisiones.

5. **Interfaz Intuitiva**: El diseño del sistema es amigable y fácil de usar, permitiendo que usuarios con conocimientos básicos de computación puedan operarlo sin dificultad.

### 4.2 Beneficios para la Clínica

La implementación de este sistema aporta los siguientes beneficios:

- **Eficiencia Operativa**: Reducción de tiempo en tareas administrativas
- **Reducción de Errores**: Validaciones automáticas previenen errores de captura
- **Mejor Atención al Paciente**: Acceso rápido a historial médico completo
- **Control Financiero**: Seguimiento detallado de pagos y tarifas
- **Cumplimiento Normativo**: Registro adecuado de información médica

### 4.3 Impacto en la Gestión Médica

El sistema transforma la gestión tradicional de una clínica al:

- Digitalizar completamente los expedientes clínicos
- Automatizar la programación de citas
- Centralizar la información de pacientes y médicos
- Facilitar la generación de reportes para toma de decisiones
- Mejorar la comunicación entre personal médico y administrativo

### 4.4 Reflexión Final

El desarrollo de este sistema ha demostrado que es posible crear soluciones tecnológicas robustas y funcionales que resuelvan problemas reales del sector salud. La combinación de tecnologías web modernas (PHP, MySQL, Bootstrap) con buenas prácticas de programación ha resultado en un sistema estable, seguro y escalable.

El proyecto no solo cumple con los requisitos técnicos establecidos, sino que aporta valor real a la gestión de clínicas médicas, mejorando la calidad del servicio y la experiencia tanto del personal como de los pacientes.

### 4.5 Agradecimientos

Agradecemos a todos los involucrados en el desarrollo y prueba de este sistema, cuyo esfuerzo y dedicación hicieron posible la creación de esta herramienta de gestión médica.

---

<div style="page-break-after: always;"></div>

## ANEXOS

### Anexo A: Glosario de Términos

- **CRUD**: Create, Read, Update, Delete (Crear, Leer, Actualizar, Eliminar)
- **CURP**: Clave Única de Registro de Población
- **Dashboard**: Panel de control
- **PDF**: Portable Document Format
- **CSV/Excel**: Comma-Separated Values (formato de hoja de cálculo)
- **Bitácora**: Registro de eventos del sistema
- **Expediente Clínico**: Historial médico del paciente
- **Rol**: Nivel de permisos de un usuario

### Anexo B: Contacto y Soporte

**Equipo de Desarrollo:** Sector 404

**Repositorio del Proyecto:**
- GitHub: [2430117-AW](https://github.com/Emm1186/2430117-AW)
- Trello: [Tablero del Proyecto](https://trello.com/invite/b/691e96482b49a519c5c24a11/ATTIa6864f0cc3b5645238549d89b2e6b2f575CE1AA1/tareas-zzz)
- Gantt: [Diagrama del Proyecto](https://drive.google.com/file/d/18rRIAPgfOgxj2Y-ygsBxkshp6BXn1bcX/view?usp=sharing)

**Servidor de Producción:**
- URL: http://134.209.49.200/2430117-AW/practicaNo9/Entrada/login.php

**Canal de YouTube:**
- [Lista de Reproducción](https://youtube.com/@manee-dm6pe?si=wPNw9AztvgJdEMgZ)

### Anexo C: Requisitos del Sistema

**Requisitos del Servidor:**
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Apache 2.4 o superior

**Requisitos del Cliente:**
- Navegador web moderno (Chrome, Firefox, Edge, Safari)
- Conexión a Internet
- Resolución mínima de pantalla: 1366x768

---

**FIN DEL MANUAL DE USUARIO**

*Versión 1.0 - Noviembre 2025*  
*Sistema de Gestión Médica - Sector 404*
