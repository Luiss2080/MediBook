# 🏥 MediBook

Base en PHP puro (arquitectura MVC, PDO + MySQL) para un sistema de gestión de citas médicas pensado para consultorios de medicina general, psicología, fisioterapia y odontología. Hoy implementa un flujo de autenticación completo y con buenas prácticas de seguridad de verdad (CSRF, bloqueo por fuerza bruta, recuperación de contraseña de un solo uso, sesiones endurecidas) con paneles separados para administrador, doctor y paciente — la capa de citas, notificaciones y reportes está planificada en el modelo de datos y las dependencias del proyecto, pero **todavía no tiene lógica de negocio implementada** (ver "Estado actual" más abajo antes de asumir que es un sistema de citas funcional).

## Estado actual

Implementado y probado:

- Registro, login y logout con contraseñas hasheadas (`password_hash`/bcrypt).
- Protección CSRF (token de un solo uso) en los formularios de login, registro, "olvidé mi contraseña" y "restablecer contraseña".
- Bloqueo de cuenta tras 5 intentos fallidos de login en 15 minutos.
- Recuperación de contraseña por token de un solo uso con expiración de 1 hora, enviado únicamente por correo (nunca se muestra en la respuesta HTTP).
- Sesiones con cookie configurable (nombre, duración, `HttpOnly`, `SameSite=Lax`, `Secure` automático bajo HTTPS) y regeneración del ID de sesión tras un login exitoso.
- Tres roles (`admin`, `doctor`, `patient`), cada uno con su propio dashboard.
- Migraciones SQL versionadas (`src/Database/migrations`) y datos semilla de usuarios de demostración.

**No implementado todavía**, aunque el README original lo describía como terminado: reserva/gestión de citas, CRUD de médicos y pacientes, notificaciones, reportes y exportación a PDF/Excel. Las clases correspondientes (`AppointmentController`, `Appointment`, `DoctorController`, `PatientController`, `NotificationController`, `ReportController`, `AppointmentService`, `NotificationService`, `ReportService`, `ValidationService`, `FileHelper`) existen como archivos vacíos en el repositorio, y el esquema de base de datos no tiene todavía una tabla de citas. Los dashboards de admin/doctor/paciente muestran contenido de demostración, no datos reales de citas. `phpmailer/phpmailer`, `dompdf/dompdf` y `phpoffice/phpspreadsheet` están declarados como dependencias para cuando se construyan esos módulos, pero ningún controlador los usa todavía (los correos de recuperación de contraseña se envían con la función `mail()` nativa de PHP).

## Características

- Autenticación con roles (`admin` / `doctor` / `patient`) y dashboards separados por rol.
- CSRF, bloqueo por fuerza bruta, recuperación de contraseña segura y sesiones endurecidas (ver arriba).
- Acceso a datos vía PDO con **prepared statements en todas las consultas** (sin concatenación de SQL en ninguna vista o modelo).
- Migraciones y seeds SQL versionados, ejecutables con un único script (`scripts/migrate.php`).
- Suite de 24 tests automatizados (PHPUnit) y pipeline de CI en GitHub Actions (lint + tests contra MySQL real en cada push/PR).

## Cómo usar

No hay todavía un router ni un punto de entrada único: cada pantalla es un archivo PHP que se sirve directamente. Con el servidor local corriendo (ver abajo):

- `http://localhost:8000/` — página de bienvenida.
- `http://localhost:8000/src/Views/auth/register.php` — crear una cuenta (siempre se registra como `patient`).
- `http://localhost:8000/src/Views/auth/login.php` — iniciar sesión. Tras `php scripts/migrate.php` quedan sembrados tres usuarios de **desarrollo únicamente** (no los dejes en una base de datos real): `admin@medibook.com`, `doctor@medibook.com` y `patient@medibook.com`, los tres con la contraseña `password123`.
- Cada rol es redirigido a su propio dashboard (`src/Views/admin|doctor|patient/dashboard.php`).

## Instalación y uso local

```bash
# 1. Clonar
git clone https://github.com/Luiss2080/MediBook.git
cd MediBook

# 2. Dependencias PHP
composer install

# 3. Configurar entorno
cp .env.example .env
# editar .env con las credenciales de tu MySQL local

# 4. Crear la base de datos
mysql -u root -e "CREATE DATABASE medibook CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"

# 5. Migraciones + datos de demostración
php scripts/migrate.php

# 6. Servidor de desarrollo — servir desde la RAÍZ del proyecto, no desde
#    public/ (esa carpeta solo contiene assets estáticos: css/js/imágenes,
#    no hay ningún index.php ahí)
php -S localhost:8000

# Abrir http://localhost:8000
```

Requiere PHP 8.1+ con las extensiones `pdo` y `pdo_mysql`, y MySQL 8 (o compatible) accesible con las credenciales de `.env`.

## Tecnologías

**Backend:** PHP 8.1+, MySQL, PDO (prepared statements), Composer.

**Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript vanilla (sin build step — no hay ningún proceso de bundling corriendo hoy pese a que `package.json` declara Webpack).

**Dependencias PHP (Composer):**

```json
{
  "phpmailer/phpmailer": "^6.9",
  "vlucas/phpdotenv": "^5.6",
  "dompdf/dompdf": "^3.1",
  "phpoffice/phpspreadsheet": "^2.2"
}
```

`phpmailer`, `dompdf` y `phpspreadsheet` están declarados para los módulos de notificaciones/reportes que aún no existen (ver "Estado actual"); hoy no los usa ningún controlador.

**Herramientas de desarrollo:** PHPUnit 10/11 (tests), PHP-CS-Fixer con reglas PSR-12 (`.php-cs-fixer.php`), GitHub Actions (`.github/workflows/ci.yml`).

## Tests

Los tests de integración necesitan una base de datos MySQL real y desechable — nunca tocan la base de datos de desarrollo.

```bash
mysql -u root -e "CREATE DATABASE medibook_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"

# Si tu MySQL local no es root/localhost/sin contraseña, exporta las
# variables DB_* correspondientes antes de correr los tests
# (ver tests/bootstrap.php para los valores por defecto).

composer test
```

CI ejecuta lo mismo automáticamente en cada push/PR contra `main`, con un contenedor de MySQL 8.0 efímero, en PHP 8.1 y 8.3.

## Licencia

MIT — ver [LICENSE](LICENSE).
