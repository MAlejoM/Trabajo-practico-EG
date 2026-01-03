# Sistema de Recuperación y Cambio de Contraseña - Veterinaria San Antón

## 📋 Descripción

Sistema completo de recuperación de contraseña ("Forgot Password") y cambio de contraseña con confirmación por email para todos los usuarios del sistema.

---

## ✨ Funcionalidades Implementadas

### 1. Recuperación de Contraseña (Forgot Password)

- Solicitud de recuperación por email
- Tokens seguros de un solo uso
- Expiración automática (1 hora)
- Emails HTML profesionales
- Validación de usuarios activos

### 2. Cambio de Contraseña desde Mi Perfil

- Disponible para **TODOS** los usuarios (Clientes, Personal, Admin)
- Validación de contraseña actual
- Mínimo 8 caracteres para nueva contraseña
- Confirmación por email automática
- Indicador de fortaleza de contraseña

### 3. Sistema de Emails

- PHPMailer integrado con Gmail SMTP
- Templates HTML responsive
- Confirmaciones automáticas
- Logs de errores

---

## 🗄️ Estructura de Base de Datos

### Tabla: `password_reset_tokens`

```sql
CREATE TABLE password_reset_tokens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    token_hash VARCHAR(255) NOT NULL,
    expira_en DATETIME NOT NULL,
    usado TINYINT(1) DEFAULT 0,
    ip_solicitud VARCHAR(45),
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES Usuarios(id) ON DELETE CASCADE
);
```

**Campos:**

- `token`: Token aleatorio de 64 caracteres (enviado por email)
- `token_hash`: Hash SHA-256 del token (doble seguridad)
- `expira_en`: Timestamp de expiración (1 hora desde creación)
- `usado`: Flag para tokens de un solo uso
- `ip_solicitud`: IP del solicitante (auditoría)

---

## 📁 Archivos del Sistema

### Backend

#### `src/logic/mail.logic.php`

Sistema de envío de emails con PHPMailer.

**Funciones:**

- `enviar_email_recuperacion($email, $nombre, $token)` - Email con link de recuperación
- `enviar_email_confirmacion_cambio($email, $nombre)` - Confirmación de cambio
- `enviar_email($destinatario, $asunto, $mensaje_html)` - Función base
- `test_email_config($email_prueba)` - Test de configuración

#### `src/logic/password_recovery.logic.php`

Lógica de recuperación de contraseña.

**Funciones:**

- `solicitar_recuperacion($email)` - Genera token y envía email
- `validar_token($token)` - Verifica validez del token
- `resetear_contrasena($token, $nueva_contrasena)` - Cambia contraseña con token
- `limpiar_tokens_expirados()` - Limpieza periódica de tokens

#### `src/logic/usuarios.logic.php`

Función actualizada:

- `cambiar_contrasena($usuario_id, $clave_actual, $nueva_clave)`
  - ✨ **NUEVO**: Envía email de confirmación
  - ✨ **NUEVO**: Mínimo 8 caracteres
  - ✨ **NUEVO**: Obtiene email y nombre del usuario

### Frontend

#### `public/forgot_password.php`

Página para solicitar recuperación de contraseña.

**Características:**

- Formulario simple con email
- Validación de email
- Mensaje de confirmación
- Instrucciones claras
- Enlace de regreso al login

#### `public/reset_password.php`

Página para crear nueva contraseña con token.

**Características:**

- Validación de token automática
- Formulario con validación
- Indicador de fortaleza de contraseña
- Mostrar/ocultar contraseña
- Mensajes de error/éxito claros

#### `public/login.php`

Actualizado con:

- Enlace "¿Olvidaste tu contraseña?"
- Icono visual

#### `public/mi_perfil.php`

Cambio de contraseña mejorado:

- ✨ **Disponible para TODOS los usuarios** (no solo clientes)
- Validación de 8 caracteres mínimo
- Confirmación por email automática

---

## 🔐 Flujo de Recuperación de Contraseña

```
1. Usuario en login.php
   ↓
2. Click en "¿Olvidaste tu contraseña?"
   ↓
3. Formulario con email (forgot_password.php)
   ↓
4. Backend valida usuario y genera token
   ↓
5. Email enviado con link único
   ↓
6. Usuario click en el link
   ↓
7. Formulario de nueva contraseña (reset_password.php)
   ↓
8. Backend valida token y cambia contraseña
   ↓
9. Email de confirmación enviado
   ↓
10. Redirige a login con nueva contraseña
```

---

## 🔒 Seguridad Implementada

### ✅ Protecciones Activas:

1. **Tokens Seguros**

   - 64 caracteres aleatorios (256 bits de entropía)
   - Hash SHA-256 almacenado en DB
   - Doble validación (token + hash)

2. **Expiración Automática**

   - Tokens válidos solo por 1 hora
   - Verificación en cada uso

3. **Un Solo Uso**

   - Token marcado como usado después del cambio
   - No reutilizable

4. **Invalidación Automática**

   - Tokens anteriores invalidados al solicitar uno nuevo
   - Solo un token activo por usuario

5. **No Revelación de Información**

   - Mensaje genérico si email no existe
   - Previene enumeración de usuarios

6. **Auditoría**

   - IP de solicitud registrada
   - Emails de confirmación
   - Logs de errores

7. **Password Hashing**

   - bcrypt con PASSWORD_DEFAULT
   - Salt automático

8. **Validación de Fortaleza**
   - Mínimo 8 caracteres
   - Indicador visual en UI

---

## 📧 Configuración de Email

### Requisitos:

1. **PHP composer** instalado
2. **PHPMailer** instalado: `composer require phpmailer/phpmailer`
3. **Gmail** con contraseña de aplicación

### Configuración en `src/config.php`:

```php
define('MAILHOST', "smtp.gmail.com");
define('USERNAME', "tu@gmail.com");           // ← Tu email
define('PASSWORD', "contraseña_aplicacion");  // ← Contraseña de app
define('SEND_FROM', "noreply@tudominio.com");
define('SEND_FROM_NAME', "Veterinaria San Antón");
```

### Obtener Contraseña de Aplicación Google:

1. Ir a https://myaccount.google.com/security
2. Activar "Verificación en 2 pasos"
3. Buscar "Contraseñas de aplicaciones"
4. Generar contraseña para "Correo" + "Windows/Mac"
5. Copiar la contraseña generada (sin espacios)
6. Pegar en `PASSWORD` en config.php

---

## 🧪 Pruebas

### Test 1: Recuperación Completa

```
1. Ir a login.php
2. Click "¿Olvidaste tu contraseña?"
3. Ingresar email de usuario existente
4. Verificar email recibido
5. Click en link del email
6. Ingresar nueva contraseña (mínimo 8 caracteres)
7. Verificar email de confirmación recibido
8. Login con nueva contraseña
✅ Resultado: Login exitoso
```

### Test 2: Token Expirado

```
1. Solicitar recuperación
2. Esperar 1 hora y 5 minutos
3. Intentar usar el link
✅ Resultado: "Token expirado"
```

### Test 3: Token Usado

```
1. Solicitar recuperación
2. Usar token para cambiar contraseña
3. Intentar usar mismo link de nuevo
✅ Resultado: "Token ya utilizado"
```

### Test 4: Cambio desde Perfil (todos los usuarios)

```
1. Login como Cliente/Personal/Admin
2. Ir a "Mi Perfil"
3. Scroll hasta "Cambiar Contraseña"
4. Completar formulario
5. Verificar email de confirmación
✅ Resultado: Contraseña cambiada, email recibido
```

### Test 5: Test de Email

```php
// En cualquier archivo PHP:
require_once 'src/logic/mail.logic.php';
$result = test_email_config('tu@email.com');
var_dump($result);
```

---

## 🛠️ Instalación

### 1. Ejecutar Migración SQL

```bash
mysql -u root veterinaria_db < migrations/create_password_reset_tokens.sql
```

O desde phpMyAdmin:

- Seleccionar base de datos `veterinaria_db`
- Pestaña SQL
- Copiar contenido de `migrations/create_password_reset_tokens.sql`
- Ejecutar

### 2. Instalar PHPMailer

```bash
cd c:\xampp\htdocs
composer require phpmailer/phpmailer
```

### 3. Configurar Email

Editar `src/config.php` con tus credenciales de Gmail (ver sección Configuración de Email arriba)

### 4. Probar

Acceder a `http://localhost/public/login.php` y probar el flujo completo

---

## ⚙️ Mantenimiento
 
Recomendación: Ejecutar diariamente

### Logs

Los errores de email se registran automáticamente en:

- `logs/app_errors.log` (si DEV_MODE = true)
- PHP error log estándar

---

## 📝 Cambios Principales

### ✅ Completado:

1. ✅ Tabla `password_reset_tokens` creada
2. ✅ Sistema de emails con PHPMailer
3. ✅ Función `solicitar_recuperacion()`
4. ✅ Función `validar_token()`
5. ✅ Función `resetear_contrasena()`
6. ✅ Página `forgot_password.php`
7. ✅ Página `reset_password.php`
8. ✅ Link en `login.php`
9. ✅ `cambiar_contrasena()` mejorada con email
10. ✅ `mi_perfil.php` actualizado para TODOS los usuarios
11. ✅ Validación 8 caracteres mínimo
12. ✅ Documentación completa

---

## ❓ Troubleshooting

### Email no se envía

1. Verificar que PHPMailer esté instalado:

   ```bash
   composer show phpmailer/phpmailer
   ```

2. Verificar credenciales en `config.php`

3. Verificar contraseña de aplicación de Google (no la contraseña normal)

4. Revisar logs:
   ```bash
   tail -f logs/app_errors.log
   ```

### Token inválido inmediatamente

- Verificar que la tabla `password_reset_tokens` existe
- Verificar zona horaria del servidor MySQL
- Verificar que la hora del servidor sea correcta

### Contraseña no cambia desde perfil

- Verificar que la contraseña actual sea correcta
- Verificar que la nueva contraseña tenga mínimo 8 caracteres
- Verificar logs de errores

---

## 📞 Soporte

Para problemas o dudas:

- Revisar logs en `logs/app_errors.log`
- Verificar configuración de email
- Ejecutar test de email: `test_email_config()`
