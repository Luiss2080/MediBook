# 🩺 MediBook - Sistema de Gestión de Citas Médicas

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![PHP](https://img.shields.io/badge/PHP-8.0+-purple.svg)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

Sistema web integral para la gestión y administración de citas médicas, diseñado para profesionales de la salud (médicos, psicólogos, fisioterapeutas, odontólogos) y sus pacientes. MediBook automatiza el proceso de reserva, seguimiento y gestión de consultas médicas con un enfoque en eficiencia y experiencia de usuario.

---

## 📋 Índice

- [Descripción General](#-descripción-general)
- [Arquitectura del Sistema](#-arquitectura-del-sistema)
- [Módulos Funcionales](#-módulos-funcionales)
- [Stack Tecnológico](#-stack-tecnológico)
- [Modelo de Datos](#-modelo-de-datos)
- [Instalación](#-instalación)
- [Configuración](#-configuración)
- [Características de Seguridad](#-características-de-seguridad)

---

## 🎯 Descripción General

MediBook es una plataforma web desarrollada con arquitectura MVC en PHP puro, que permite la gestión completa del ciclo de vida de las citas médicas, desde la solicitud inicial hasta el registro post-consulta. El sistema maneja tres roles principales: **Administradores**, **Profesionales de Salud** y **Pacientes**, cada uno con interfaces y permisos específicos.

### Objetivos del Sistema

- **Automatización**: Eliminar procesos manuales en la gestión de agendas médicas
- **Accesibilidad**: Permitir reservas 24/7 desde cualquier dispositivo
- **Trazabilidad**: Mantener historial completo de consultas y tratamientos
- **Comunicación**: Notificaciones automáticas para reducir ausencias
- **Eficiencia**: Optimizar el uso del tiempo del profesional de salud

---

## 🏗️ Arquitectura del Sistema

### Patrón de Diseño

- **MVC (Model-View-Controller)**: Separación clara de lógica de negocio, presentación y datos
- **Repository Pattern**: Abstracción de acceso a datos
- **Service Layer**: Lógica de negocio centralizada y reutilizable
- **Middleware Pattern**: Manejo de autenticación, autorización y validaciones

### Estructura de Capas

```
┌─────────────────────────────────────┐
│     Capa de Presentación (Views)    │
│   HTML5 + Bootstrap 5 + JavaScript  │
└─────────────────┬───────────────────┘
                  │
┌─────────────────▼───────────────────┐
│   Capa de Controladores (MVC)       │
│   Enrutamiento + Lógica de Control  │
└─────────────────┬───────────────────┘
                  │
┌─────────────────▼───────────────────┐
│    Capa de Servicios (Business)     │
│  Lógica de Negocio + Validaciones   │
└─────────────────┬───────────────────┘
                  │
┌─────────────────▼───────────────────┐
│   Capa de Modelos (Data Access)     │
│      PDO + MySQL + Migrations       │
└─────────────────────────────────────┘
```

---

## 🧩 Módulos Funcionales

### 1️⃣ Módulo de Autenticación y Usuarios

**Funcionalidades:**

- Registro de usuarios con validación de correo electrónico
- Login/Logout con sesiones seguras
- Recuperación de contraseña mediante token temporal
- Gestión de roles y permisos (RBAC - Role-Based Access Control)
- Perfil de usuario con avatar personalizable
- Registro de actividad y último acceso

**Roles del Sistema:**

- `admin`: Acceso completo al sistema
- `medico`: Gestión de agenda, pacientes y consultas
- `paciente`: Reserva y seguimiento de citas

### 2️⃣ Módulo de Profesionales de Salud (Médicos)

**Funcionalidades:**

- **Perfil Profesional Completo:**

  - Datos personales y credenciales (número de licencia)
  - Especialidad médica
  - Experiencia profesional y educación
  - Foto de perfil y biografía
  - Idiomas atendidos
  - Ubicación del consultorio

- **Gestión de Disponibilidad:**

  - Configuración de horarios de atención (inicio/fin)
  - Días laborables (lunes a domingo)
  - Duración predeterminada de consultas (15, 30, 45, 60 min)
  - Bloqueo de horarios específicos (vacaciones, emergencias)
  - Estados: Activo, Inactivo, En vacaciones

- **Panel de Control:**

  - Dashboard con métricas del día
  - Calendario visual de citas (FullCalendar)
  - Lista de pacientes atendidos
  - Gestión de citas pendientes de confirmación
  - Filtros por fecha, estado y tipo de consulta

- **Gestión de Consultas:**
  - Aprobar, rechazar o reprogramar citas
  - Registro de diagnóstico y tratamiento
  - Prescripción de medicamentos (recetas)
  - Solicitud de exámenes médicos
  - Observaciones y notas clínicas
  - Registro de signos vitales

### 3️⃣ Módulo de Pacientes

**Funcionalidades:**

- **Perfil Médico Completo:**

  - Datos personales (CI, fecha nacimiento, edad)
  - Información de contacto (dirección, teléfono, email)
  - Contacto de emergencia
  - Grupo sanguíneo
  - Estado civil y ocupación
  - Seguro médico y número de póliza

- **Historia Clínica:**

  - Alergias conocidas
  - Enfermedades crónicas
  - Medicamentos actuales
  - Cirugías previas
  - Antecedentes familiares

- **Gestión de Citas:**

  - Búsqueda de médicos por especialidad
  - Visualización de disponibilidad en tiempo real
  - Solicitud de cita con selección de fecha/hora
  - Cancelación o reprogramación de citas
  - Historial completo de consultas
  - Próximas citas programadas

- **Documentación:**
  - Descarga de recetas médicas en PDF
  - Acceso a resultados de exámenes
  - Historial de diagnósticos y tratamientos

### 4️⃣ Módulo de Citas (Core del Sistema)

**Funcionalidades:**

- **Sistema de Reservas:**

  - Calendario interactivo con disponibilidad en tiempo real
  - Validación automática de conflictos de horario
  - Confirmación inmediata o pendiente (según configuración)
  - Selección de tipo de consulta (Primera vez, Seguimiento, Urgencia, Control)
  - Campo de motivo de consulta y síntomas

- **Estados de Cita:**

  - `pendiente`: Solicitada por paciente, esperando confirmación
  - `confirmada`: Aprobada por el médico
  - `en_proceso`: Consulta en curso
  - `completada`: Finalizada con registro médico
  - `cancelada`: Cancelada con motivo registrado
  - `no_asistio`: Paciente no se presentó

- **Gestión de Agenda:**

  - Vista diaria, semanal y mensual
  - Código de colores por estado
  - Drag & drop para reprogramar (solo médicos)
  - Filtros múltiples (médico, paciente, estado, fecha)
  - Búsqueda rápida

- **Flujo de Cita:**
  1. Paciente solicita cita → Estado: `pendiente`
  2. Sistema valida disponibilidad
  3. Médico revisa y confirma → Estado: `confirmada`
  4. Sistema envía confirmación por email
  5. 24h antes: Envío de recordatorio automático
  6. Durante consulta → Estado: `en_proceso`
  7. Post-consulta: Registro médico → Estado: `completada`

### 5️⃣ Módulo de Notificaciones

**Funcionalidades:**

- **Sistema Multi-canal:**

  - Notificaciones in-app (campana de notificaciones)
  - Correo electrónico (PHPMailer + SMTP)
  - Historial de notificaciones

- **Tipos de Notificación:**

  - `cita_confirmada`: Confirmación de cita reservada
  - `cita_cancelada`: Aviso de cancelación
  - `recordatorio`: Recordatorio 24h antes de la cita
  - `cita_reprogramada`: Cambio de fecha/hora
  - `mensaje`: Comunicación directa médico-paciente
  - `sistema`: Actualizaciones y mantenimiento

- **Automatización:**

  - Job/Cron para envío de recordatorios automáticos
  - Cola de correos para evitar bloqueos
  - Reintentos automáticos en caso de fallo
  - Registro de historial de emails enviados

- **Características:**
  - Prioridad (baja, media, alta)
  - Estados: No leída, Leída
  - Marca de tiempo
  - Enlace directo a la acción relacionada
  - Badge con contador de no leídas

### 6️⃣ Módulo de Reportes y Estadísticas

**Funcionalidades:**

- **Reportes Operativos:**

  - Citas por médico (diario, semanal, mensual)
  - Citas por paciente (historial completo)
  - Tasa de cancelaciones y ausencias
  - Horarios con mayor demanda
  - Especialidades más solicitadas
  - Pacientes nuevos vs recurrentes

- **Reportes Financieros:**

  - Ingresos por médico
  - Ingresos por especialidad
  - Métodos de pago utilizados
  - Citas pagadas vs pendientes de pago

- **Estadísticas Visuales:**

  - Gráficos de barras (Chart.js)
  - Gráficos de líneas (tendencias)
  - Gráficos circulares (distribución)
  - Dashboards interactivos

- **Exportación:**
  - PDF (DomPDF)
  - Excel (PhpSpreadsheet)
  - CSV para análisis externo
  - Impresión directa

### 7️⃣ Módulo de Administración

**Funcionalidades:**

- **Gestión de Usuarios:**

  - CRUD completo de usuarios
  - Asignación de roles
  - Activación/Suspensión de cuentas
  - Reseteo de contraseñas
  - Registro de actividad

- **Gestión de Médicos:**

  - Aprobación de registro de médicos
  - Configuración de honorarios
  - Gestión de especialidades
  - Asignación de consultorios

- **Gestión de Pacientes:**

  - Visualización de pacientes registrados
  - Historial médico completo
  - Gestión de documentos

- **Configuración del Sistema:**
  - Parámetros generales (duración citas, horarios)
  - Plantillas de correo electrónico
  - Configuración de notificaciones
  - Mantenimiento de base de datos
  - Logs del sistema

---

## 🛠️ Stack Tecnológico

### Backend

- **PHP 8.0+**: Lenguaje principal con tipado estricto y características modernas
- **MySQL 8.0+**: Base de datos relacional
- **PDO**: Capa de abstracción de base de datos con prepared statements
- **Composer**: Gestor de dependencias PHP

### Frontend

- **HTML5**: Estructura semántica
- **CSS3**: Estilos con variables CSS y Flexbox/Grid
- **Bootstrap 5**: Framework CSS responsive
- **JavaScript ES6+**: Interactividad del lado del cliente
- **FullCalendar.js 6.x**: Calendario interactivo de citas
- **Chart.js 4.x**: Gráficos y visualización de datos
- **SweetAlert2**: Modales y alertas elegantes
- **Axios**: Cliente HTTP para AJAX

### Librerías PHP (Composer)

```json
{
  "phpmailer/phpmailer": "^6.9", // Envío de correos
  "vlucas/phpdotenv": "^5.6", // Variables de entorno
  "dompdf/dompdf": "^2.0", // Generación de PDFs
  "phpoffice/phpspreadsheet": "^1.29" // Excel export/import
}
```

### Herramientas de Desarrollo

- **PHPUnit**: Testing unitario
- **PHP-CS-Fixer**: Estándares de código (PSR-12)
- **Git**: Control de versiones
- **Webpack**: Bundling de assets frontend

---

## 🗄️ Modelo de Datos

### Diagrama Entidad-Relación (Simplificado)

```
┌─────────────┐       ┌──────────────┐       ┌─────────────┐
│  USUARIOS   │──────▶│   MÉDICOS    │       │  PACIENTES  │◀──────┐
│             │       │              │       │             │       │
│ id          │       │ id           │       │ id          │       │
│ nombre      │       │ id_usuario   │       │ id_usuario  │       │
│ correo      │       │ especialidad │       │ ci          │       │
│ contraseña  │       │ horarios     │       │ historial   │       │
│ rol         │       │ consultorio  │       │ alergias    │       │
└─────────────┘       └──────────────┘       └─────────────┘       │
                              │                      │              │
                              │                      │              │
                              └──────────┬───────────┘              │
                                         │                          │
                                         ▼                          │
                              ┌──────────────────┐                  │
                              │      CITAS       │                  │
                              │                  │                  │
                              │ id               │                  │
                              │ id_paciente      │──────────────────┘
                              │ id_medico        │
                              │ fecha            │
                              │ hora_inicio      │
                              │ hora_fin         │
                              │ estado           │
                              │ diagnostico      │
                              │ tratamiento      │
                              └──────────────────┘
                                         │
                                         │
                                         ▼
                              ┌──────────────────┐
                              │  NOTIFICACIONES  │
                              │                  │
                              │ id               │
                              │ id_usuario       │
                              │ tipo             │
                              │ mensaje          │
                              │ leido            │
                              └──────────────────┘
```

### Tablas Principales

#### `usuarios`

Almacena información base de todos los usuarios del sistema.

- **Campos clave**: id, nombre, correo, contraseña (hash), rol, estado
- **Relaciones**: 1:1 con médicos o pacientes según rol

#### `medicos`

Información específica del profesional de salud.

- **Campos clave**: especialidad, horarios, consultorio, costo_consulta
- **Índices**: especialidad, estado, id_usuario

#### `pacientes`

Datos médicos y personales del paciente.

- **Campos clave**: ci, historial, alergias, grupo_sanguíneo
- **Índices**: ci, id_usuario

#### `citas`

Núcleo del sistema, gestiona todas las reservas.

- **Campos clave**: fecha, hora, estado, diagnóstico, tratamiento
- **Índices**: fecha, estado, médico, paciente
- **Constraint**: UNIQUE(id_medico, fecha, hora_inicio) - Previene doble reserva

#### `notificaciones`

Sistema de mensajería y alertas.

- **Campos clave**: tipo, mensaje, leído, prioridad
- **Índices**: usuario, leído, fecha

---

## 🚀 Instalación

### Requisitos del Sistema

```bash
PHP >= 8.0
MySQL >= 8.0
Composer >= 2.0
Node.js >= 16.x (opcional, para assets)
Apache/Nginx con mod_rewrite
```

### Instalación Paso a Paso

```bash
# 1. Clonar repositorio
git clone https://github.com/tu-usuario/medibook.git
cd medibook

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias Node (opcional)
npm install

# 4. Configurar entorno
cp .env.example .env
nano .env  # Editar con tus credenciales

# 5. Ejecutar script de setup
bash scripts/setup.sh

# 6. Crear base de datos
mysql -u root -p
CREATE DATABASE medibook CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# 7. Ejecutar migraciones
php scripts/migrate.php

# 8. Poblar datos de prueba (opcional)
php scripts/seed.php

# 9. Iniciar servidor de desarrollo
php -S localhost:8000 -t public/

# Acceder a: http://localhost:8000
```

---

## ⚙️ Configuración

### Variables de Entorno (.env)

```env
# Aplicación
APP_NAME=MediBook
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de Datos
DB_HOST=localhost
DB_DATABASE=medibook
DB_USERNAME=root
DB_PASSWORD=tu_contraseña

# Correo (Gmail ejemplo)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-correo@gmail.com
MAIL_PASSWORD=contraseña-app
MAIL_ENCRYPTION=tls

# Notificaciones
NOTIFICATION_REMINDER_HOURS=24
```

### Configuración de Correo

Para Gmail, crear "Contraseña de aplicación":

1. Ir a Cuenta de Google → Seguridad
2. Verificación en dos pasos (activar)
3. Contraseñas de aplicaciones → Generar
4. Usar esa contraseña en `MAIL_PASSWORD`

---

## 🔒 Características de Seguridad

### Implementadas

- ✅ **Contraseñas Hasheadas**: `password_hash()` con BCRYPT
- ✅ **Prepared Statements**: PDO previene SQL Injection
- ✅ **CSRF Protection**: Tokens en formularios POST
- ✅ **XSS Prevention**: Sanitización con `htmlspecialchars()`
- ✅ **Session Security**:
  - `httponly` y `secure` flags
  - Regeneración de ID tras login
  - Timeout de sesión configurable
- ✅ **Input Validation**: Servidor y cliente
- ✅ **Role-Based Access Control (RBAC)**
- ✅ **Rate Limiting**: En endpoints sensibles (login, registro)
- ✅ **HTTPS Ready**: Configuración para SSL/TLS
- ✅ **Logs de Auditoría**: Registro de acciones críticas

### Buenas Prácticas

```php
// ❌ NUNCA hacer esto
$query = "SELECT * FROM usuarios WHERE email = '$email'";

// ✅ SIEMPRE usar prepared statements
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
$stmt->execute(['email' => $email]);
```

---

## 📊 Roadmap

### Fase 1 - MVP (Actual)

- [x] Sistema de autenticación
- [x] Gestión de citas básica
- [x] Notificaciones por email
- [x] Dashboard básico

### Fase 2 - Q2 2025

- [ ] Videoconsultas (WebRTC)
- [ ] Firma digital de recetas
- [ ] Integración con pasarelas de pago
- [ ] App móvil (React Native)

### Fase 3 - Q3 2025

- [ ] Historia clínica electrónica completa
- [ ] Integración con laboratorios
- [ ] Sistema de farmacia integrado
- [ ] Multi-idioma (i18n)

---

## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Consulta el archivo [LICENSE](LICENSE) para más detalles.

---

## 👨‍💻 Contribuciones

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama (`git checkout -b feature/AmazingFeature`)
3. Commit cambios (`git commit -m 'Add: nueva característica'`)
4. Push (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

---

## 📞 Soporte

- 📧 Email: soporte@medibook.com
- 🐛 Issues: [GitHub Issues](https://github.com/tu-usuario/medibook/issues)
- 📖 Docs: [Documentación Completa](https://docs.medibook.com)

---

**Desarrollado con ❤️ para mejorar la gestión de salud**
