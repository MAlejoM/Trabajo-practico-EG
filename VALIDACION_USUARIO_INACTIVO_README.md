# Sistema de Validación de Usuarios Inactivos - Veterinaria San Antón

## 📋 Descripción de la Funcionalidad

Esta funcionalidad implementa la **validación del campo `activo`** durante el proceso de inicio de sesión, bloqueando el acceso a usuarios que hayan sido dados de baja lógicamente en el sistema.

---

## 🔒 Objetivos de Seguridad

### Prevenir Acceso No Autorizado

- Solo usuarios con `activo = true` pueden autenticarse
- Los usuarios inactivos no pueden generar sesión aunque conozcan sus credenciales

### Mensajes Seguros

- Se utilizan **mensajes genéricos** para evitar la enumeración de usuarios (user enumeration)
- No se revela si un usuario existe pero está inactivo
- No se diferencia entre "usuario inactivo", "contraseña incorrecta" o "usuario inexistente"

---

## 🛠️ Implementación Técnica

### 1. Backend - Lógica de Autenticación

Archivo: `src/logic/auth.logic.php`

**Flujo de validación:**

```
1. Buscar usuario por email (SIN filtrar por activo)
   ↓
2. ¿Usuario existe?
   NO → Retornar error genérico
   SÍ → Continuar
   ↓
3. ¿Usuario activo?
   NO → Retornar error genérico (sin revelar motivo)
   SÍ → Continuar
   ↓
4. ¿Contraseña válida?
   NO → Retornar error genérico
   SÍ → Crear sesión y autenticar
```

**Código clave:**

```php
// Buscar usuario SIN filtrar por activo
$stmt = $db->prepare("
  SELECT u.id, u.email, u.clave, u.nombre, u.apellido, u.activo,
         p.id as personal_id, c.id as cliente_id, r.nombre as rol_nombre
  FROM Usuarios u
  LEFT JOIN Personal p ON p.usuarioId = u.id
  LEFT JOIN Clientes c ON c.usuarioId = u.id
  LEFT JOIN Roles r ON p.rolId = r.id
  WHERE u.email = ?
");

// Validar que el usuario está activo ANTES de verificar contraseña
if ($usuario['activo'] == 0) {
  header('Location: login.php?error=1');
  exit();
}
```

**Características importantes:**

- ✅ Se verifica el estado `activo` ANTES de validar la contraseña
- ✅ Siempre cierra la conexión a la base de datos (`$db->close()`)
- ✅ Usa `exit()` después de cada `header()` para detener la ejecución
- ✅ Retorna el mismo código de error (`?error=1`) para todos los fallos

### 2. Frontend - Página de Login

Archivo: `public/login.php`

**Mensaje de error:**

```php
<?php
if (isset($_GET['error']) && $_GET['error'] == 1) {
  echo "<div class='alert alert-danger mb-3'>
          <strong>No se pudo iniciar sesión.</strong><br>
          Verifique sus credenciales o contacte con administración.
        </div>";
}
?>
```

**Características:**

- ✅ Mensaje genérico y profesional
- ✅ Sugiere contactar con administración (útil para casos legítimos)
- ✅ No revela información sobre la existencia del usuario

---

## 🎯 Casos de Uso

### Caso 1: Usuario Activo - Login Exitoso ✅

**Escenario:**

- Email: `cliente@ejemplo.com`
- Contraseña: correcta
- Estado: `activo = 1`

**Resultado:**

- ✅ Login exitoso
- ✅ Sesión creada con variables: `usuarioId`, `nombre`, `apellido`, `cliente_id` o `personal_id`
- ✅ Redirección a `index.php`

### Caso 2: Usuario Inactivo - Login Bloqueado ❌

**Escenario:**

- Email: `cliente@ejemplo.com`
- Contraseña: correcta
- Estado: `activo = 0`

**Resultado:**

- ❌ Login rechazado
- ❌ No se crea sesión
- 🔒 Mensaje: "No se pudo iniciar sesión. Verifique sus credenciales o contacte con administración."
- ⚠️ No se revela que el usuario está inactivo

### Caso 3: Contraseña Incorrecta ❌

**Escenario:**

- Email: `cliente@ejemplo.com`
- Contraseña: incorrecta
- Estado: `activo = 1`

**Resultado:**

- ❌ Login rechazado
- 🔒 Mensaje: "No se pudo iniciar sesión. Verifique sus credenciales o contacte con administración."
- ⚠️ Mismo mensaje que caso de usuario inactivo (seguridad)

### Caso 4: Usuario No Existe ❌

**Escenario:**

- Email: `noexiste@ejemplo.com`
- Contraseña: cualquiera

**Resultado:**

- ❌ Login rechazado
- 🔒 Mensaje: "No se pudo iniciar sesión. Verifique sus credenciales o contacte con administración."
- ⚠️ No se revela que el usuario no existe

---

## 🔍 Relación con Bajas Lógicas

Esta validación es parte integral del sistema de bajas lógicas:

1. **Cuando un administrador da de baja un usuario:**

   - Se actualiza `activo = 0` en la tabla `Usuarios`
   - El registro permanece en la base de datos

2. **Efecto inmediato:**

   - Si el usuario tenía sesión abierta, continúa hasta que cierre sesión
   - Al intentar un nuevo login, es rechazado automáticamente
   - Mantiene integridad referencial con mascotas, atenciones, etc.

3. **Reactivación:**
   - El administrador puede reactivar el usuario (`activo = 1`)
   - El usuario puede volver a iniciar sesión de inmediato

---

## ✅ Criterios de Aceptación

| Criterio                                             | Estado | Verificación                        |
| ---------------------------------------------------- | ------ | ----------------------------------- |
| Usuario con `activo = true` puede iniciar sesión     | ✅     | Funciona normalmente                |
| Usuario con `activo = false` NO puede iniciar sesión | ✅     | Login bloqueado                     |
| Mensaje claro para el usuario                        | ✅     | Mensaje genérico mostrado           |
| No se genera sesión para usuario inactivo            | ✅     | No se llama a `session_start()`     |
| Seguridad: no revelar estado del usuario             | ✅     | Mensaje genérico en todos los casos |

---

## 🧪 Cómo Probar

### Preparación:

1. Crear dos usuarios de prueba:
   - Usuario A: `activo = 1` (activo)
   - Usuario B: `activo = 0` (inactivo)

### Test 1: Login con Usuario Activo

```
1. Ir a login.php
2. Ingresar credenciales del Usuario A
3. Resultado esperado: Login exitoso → index.php
```

### Test 2: Login con Usuario Inactivo

```
1. Ir a login.php
2. Ingresar credenciales correctas del Usuario B
3. Resultado esperado:
   - Permanece en login.php
   - URL: login.php?error=1
   - Mensaje: "No se pudo iniciar sesión. Verifique sus credenciales o contacte con administración."
```

### Test 3: Verificar que no se crea sesión

```
1. Intentar login con usuario inactivo (Test 2)
2. Abrir DevTools → Application → Cookies/Storage
3. Verificar que NO existen variables de sesión
4. Intentar acceder directamente a index.php
5. Resultado esperado: Redirige a login.php
```

### Test 4: Contraseña incorrecta

```
1. Usar email de Usuario A (activo)
2. Ingresar contraseña incorrecta
3. Resultado esperado: Mismo mensaje que Test 2
```

### Test 5: Reactivación de usuario

```
1. Desde usuario administrador, reactivar Usuario B
2. Actualizar: UPDATE Usuarios SET activo = 1 WHERE ...
3. Intentar login con Usuario B
4. Resultado esperado: Login exitoso
```

---

## 🔐 Consideraciones de Seguridad

### ✅ Implementado:

1. **Prevención de User Enumeration:**

   - Mensaje genérico para todos los errores de login
   - No se diferencia entre usuario inactivo, inexistente, o contraseña incorrecta

2. **Protección contra SQL Injection:**

   - Uso de prepared statements con `bind_param()`

3. **Cierre seguro de conexiones:**

   - Se cierra `$db` y `$stmt` en todos los caminos de ejecución

4. **No generación de sesión:**
   - Solo se llama a `session_start()` cuando todas las validaciones pasaron

### 📝 Recomendaciones adicionales (futuro):

1. **Logging de intentos fallidos:**

   - Registrar intentos de login de usuarios inactivos
   - Detectar intentos de fuerza bruta

2. **Rate limiting:**

   - Limitar intentos de login por IP
   - Bloqueo temporal después de X intentos fallidos

3. **Notificación al administrador:**
   - Enviar email cuando un usuario inactivo intenta acceder
   - Útil para detectar uso indebido de credenciales

---

## 📊 Base de Datos

### Tabla: `Usuarios`

| Campo        | Tipo           | Descripción                                   |
| ------------ | -------------- | --------------------------------------------- |
| `id`         | INT            | ID único del usuario                          |
| `email`      | VARCHAR(255)   | Email (único)                                 |
| `clave`      | VARCHAR(255)   | Contraseña hasheada                           |
| `nombre`     | VARCHAR(100)   | Nombre                                        |
| `apellido`   | VARCHAR(100)   | Apellido                                      |
| **`activo`** | **TINYINT(1)** | **Estado del usuario (1=activo, 0=inactivo)** |

### Queries relevantes:

```sql
-- Buscar usuario para login (actual)
SELECT u.id, u.email, u.clave, u.nombre, u.apellido, u.activo
FROM Usuarios u
WHERE u.email = ?

-- Dar de baja un usuario
UPDATE Usuarios SET activo = 0 WHERE id = ?

-- Reactivar un usuario
UPDATE Usuarios SET activo = 1 WHERE id = ?
```

---

## 📚 Archivos Involucrados

```
📁 Proyecto
├── 📄 src/logic/auth.logic.php          ✏️ MODIFICADO - Lógica de validación
├── 📄 public/login.php                  ✏️ MODIFICADO - Mensaje de error
└── 📄 VALIDACION_USUARIO_INACTIVO.md    ✨ NUEVO - Este documento
```

---

## 🎓 Diferencias con la Implementación Anterior

### ❌ Antes:

```php
// Filtrado directo en SQL
WHERE u.email = ? AND u.activo = 1

// Problema: No podíamos saber si el usuario estaba inactivo o no existía
```

### ✅ Ahora:

```php
// Busca sin filtrar
WHERE u.email = ?

// Validación explícita
if ($usuario['activo'] == 0) {
  // Bloquear acceso
}

// Ventaja: Control total del flujo y mensajes de error
```

---

## 📞 Soporte y Mantenimiento

Para cambios futuros relacionados con esta funcionalidad:

1. **Cambiar el mensaje de error:** Editar `public/login.php` línea ~30
2. **Modificar lógica de validación:** Editar `src/logic/auth.logic.php` función `procesar_login()`
3. **Ver usuarios inactivos:** Usar el toggle en `public/usuarios/usuario_list.php`
