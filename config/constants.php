<?php
/**
 * Constantes de la aplicación MediBook
 */

// Roles de usuario
define('ROLE_ADMIN', 'admin');
define('ROLE_DOCTOR', 'doctor');
define('ROLE_PATIENT', 'patient');

// Estados de citas
define('APPOINTMENT_PENDING', 'pending');
define('APPOINTMENT_CONFIRMED', 'confirmed');
define('APPOINTMENT_COMPLETED', 'completed');
define('APPOINTMENT_CANCELLED', 'cancelled');

// Estados de notificaciones
define('NOTIFICATION_UNREAD', 'unread');
define('NOTIFICATION_READ', 'read');

// Tipos de notificaciones
define('NOTIFICATION_APPOINTMENT', 'appointment');
define('NOTIFICATION_REMINDER', 'reminder');
define('NOTIFICATION_SYSTEM', 'system');

// Configuración de archivos
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('ALLOWED_DOCUMENT_TYPES', ['pdf', 'doc', 'docx']);

// Configuración de paginación
define('DEFAULT_PAGE_SIZE', 10);
define('MAX_PAGE_SIZE', 100);