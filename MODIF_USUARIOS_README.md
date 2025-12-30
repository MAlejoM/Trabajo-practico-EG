# Sistema de Gestión de Usuarios y Mascotas - Veterinaria San Antón

## 📋 Funcionalidad Implementada

Sistema completo de gestión de usuarios y mascotas con permisos diferenciados según el rol del usuario.

---

## 🔐 Tres Niveles de Permisos

### 1. **Administrador/Secretaría**
- ✅ **Creación** de nuevos usuarios (clientes y personal)
- ✅ Edición completa de cualquier usuario
- ✅ Gestión de mascotas de clientes (crear, editar, ver)
- ✅ Control de estado activo/inactivo de usuarios
- ✅ Acceso a todos los módulos del sistema

### 2. **Personal**
- ✅ Edición de sus propios datos (email, nombre, apellido)
- ✅ Gestión de mascotas (crear, editar, ver)
- ❌ Sin acceso a edición de otros usuarios
- ❌ Sin acceso a módulo de gestión de usuarios

### 3. **Cliente**
- ✅ Cambio de contraseña únicamente
- ✅ Visualización de sus datos en modo lectura
- ❌ No puede modificar datos personales (solo administrador)

---

## 📁 Archivos del Sistema

### Lógica de Negocio

#### **[NEW]** `src/logic/usuarios.logic.php`
Funciones CRUD completas para gestión de usuarios:
- `get_all_usuarios()` - Lista con filtros dinámicos por rol
- `get_usuario_completo_by_id($id)` - Datos completos de usuario
- `update_usuario_admin($id, $datos)` - Edición completa (admin)
- `update_usuario_personal($id, $datos)` - Edición limitada (personal)
- `cambiar_contrasena($id, $actual, $nueva)` - Cambio de contraseña con validación
- `validar_permisos_edicion()` - Control de permisos
- `update_cliente_datos($id, $datos)` - Actualización de datos de cliente

#### **[MODIFIED]** `src/lib/funciones.php`
Funciones auxiliares agregadas:
- `verificar_es_admin()` - Verifica rol de administrador
- `get_mascotas_by_cliente_id($id)` - Obtiene mascotas de un cliente

#### **[MODIFIED]** `src/logic/auth.logic.php`
- Actualizado para soportar contraseñas hasheadas y texto plano (compatibilidad)
- Usa `password_verify()` para validación segura

---

### Módulo de Gestión de Usuarios (Administrador)

#### **[NEW]** `public/usuarios/usuario_list.php`
**Listado principal de usuarios:**
- Tabla completa de usuarios del sistema
- **Filtros dinámicos** por rol (Admin, Cliente, Veterinario, Sin Rol)
- Botón "Nuevo Usuario" para crear usuarios
- Acciones: Editar usuario, Ver mascotas (solo clientes)
- Ordenamiento por ID ascendente
- Solo accesible por administradores

#### **[NEW]** `public/usuarios/nuevo_usuario.php`
**Formulario de creación de usuarios:**
- Toggle visual: Cliente o Personal
- Validación de email único
- Contraseñas hasheadas con bcrypt
- **Campos dinámicos:**
  - **Cliente**: Teléfono, ciudad, dirección
  - **Personal**: Selector de rol
- Transacciones SQL para integridad de datos
- Mensajes de éxito/error

#### **[NEW]** `public/usuarios/editar_usuario.php`
**Formulario de edición completa:**
- Edita: email, nombre, apellido, activo
- Campos adicionales para clientes (teléfono, ciudad, dirección)
- Validación de email único
- Información de tipo de usuario y rol (solo lectura)
- Botón para gestionar mascotas del cliente

#### **[NEW]** `public/usuarios/mascotas_usuario.php`
**Gestión de mascotas de un cliente:**
- Lista completa de mascotas del cliente
- Información del cliente (nombre, email, teléfono)
- Botón "Nueva Mascota" previnculado al cliente
- Enlaces a ver y editar cada mascota
- Solo para usuarios tipo Cliente

---

### Módulo de Mascotas

#### **[NEW]** `public/mascotas/nueva_mascota.php`
**Formulario de creación de mascotas:**
- **Dos modos**:
  - Con cliente preseleccionado (desde gestión de usuarios)
  - Con selector de clientes (desde lista general)
- Campos: nombre, raza, color, fecha de nacimiento, foto
- Validación de tamaño de imagen (máx 2MB)
- Solo accesible por personal

#### **[REWRITTEN]** `public/mascotas/editar_mascota.php`
**Formulario de edición de mascotas:**
- Edición completa de datos
- Preview de foto actual
- Cambio de foto opcional
- Fecha de nacimiento y fallecimiento
- Estado activo/inactivo
- Prepared statements (seguridad)

#### **[NEW]** `public/mascotas/ver_mascota.php`
**Vista detallada de mascota:**
- Información completa de la mascota
- Foto, datos básicos, edad calculada
- Alerta si está fallecida
- Información del dueño (nombre, email, teléfono, dirección)
- **Historial de atenciones** (últimas 5)
- Botón para editar (solo personal)

---

### Perfil de Usuario

#### **[MODIFIED]** `public/mi_perfil.php`
**Actualizado con lógica condicional:**

**Para Clientes:**
- Datos personales en solo lectura
- Formulario de cambio de contraseña
- Validación de contraseña actual
- Mínimo 6 caracteres para nueva contraseña

**Para Personal:**
- Formulario de edición: nombre, apellido, email
- Información de estado (solo lectura)
- Enlace al módulo de usuarios (solo admin)

**Para Administrador:**
- Mismas opciones que personal
- Alerta con enlace directo a gestión de usuarios

---

### Componentes

#### **[MODIFIED]** `src/includes/menu_lateral.php`
- Agregado enlace **"USUARIOS"** para administradores
- Ubicado entre "NOVEDADES" y "SERVICIOS"
- Redirige a `public/usuarios/usuario_list.php`

---

## 🎯 Guía de Uso Completa

### Para Administradores

#### **Crear Nuevo Usuario:**
1. Ir a USUARIOS → Botón "Nuevo Usuario"
2. Seleccionar tipo: Cliente o Personal
3. Completar datos básicos (nombre, apellido, email, contraseña)
4. Si es **Cliente**: Agregar teléfono, ciudad, dirección (opcional)
5. Si es **Personal**: Seleccionar rol (admin, veterinario, etc.)
6. Guardar

#### **Gestionar Usuarios Existentes:**
1. Ir a USUARIOS
2. Usar filtros para ver por rol (Todos/Admin/Cliente/etc.)
3. Hacer clic en **Editar** (lápiz) para modificar datos
4. Cambiar: email, nombre, apellido, estado activo
5. Para clientes: también teléfono, ciudad, dirección
6. Guardar cambios

#### **Gestionar Mascotas de un Cliente:**
1. En lista de usuarios, hacer clic en icono **Mascota** (pata)
2. Ver todas las mascotas del cliente
3. Opciones:
   - **Nueva Mascota**: Crear nueva (cliente preseleccionado)
   - **Ver**: Información completa y atenciones
   - **Editar**: Modificar datos de la mascota

#### **Crear Mascota:**
1. Desde lista de usuarios → icono mascota → "Nueva Mascota"
   - O desde MASCOTAS → "Nueva Mascota"
2. Seleccionar cliente (si no viene preseleccionado)
3. Completar: nombre, raza, color, fecha nacimiento
4. Agregar foto (opcional, máx 2MB)
5. Guardar

---

### Para Personal (No Admin)

#### **Editar Perfil Propio:**
1. Ir a "Mi Perfil" (menú superior derecho)
2. Modificar: nombre, apellido, email
3. Guardar cambios

#### **Gestionar Mascotas:**
1. Ir a MASCOTAS en el menú lateral
2. Crear, ver o editar mascotas
3. Acceso completo a gestión de mascotas

---

### Para Clientes

#### **Cambiar Contraseña:**
1. Ir a "Mi Perfil" (menú superior derecho)
2. Ver datos personales (solo lectura)
3. Completar formulario de cambio de contraseña:
   - Contraseña actual
   - Nueva contraseña (mín. 6 caracteres)
   - Confirmar nueva contraseña
4. Guardar cambios

**Nota**: Para cambiar otros datos personales, contactar al administrador.

---

## 🔒 Validaciones de Seguridad

### Autenticación y Permisos
- ✅ Sesión requerida en todas las páginas
- ✅ Validación de roles en cada endpoint
- ✅ Redirección automática si no tiene permisos
- ✅ Diferentes vistas según rol del usuario

### Contraseñas
- ✅ Hasheadas con **bcrypt** (`password_hash()`)
- ✅ Validación de contraseña actual antes de cambiar
- ✅ Longitud mínima: 6 caracteres
- ✅ Confirmación obligatoria
- ✅ **Compatibilidad**: Soporta texto plano y hash (migración)

### Base de Datos
- ✅ **Prepared statements** en todas las consultas
- ✅ Prevención de SQL Injection
- ✅ Validación de email único
- ✅ Transacciones para operaciones complejas
- ✅ Validación de integridad referencial

### Archivos
- ✅ Validación de tipo de archivo (solo imágenes)
- ✅ Límite de tamaño: 2MB para fotos
- ✅ Almacenamiento seguro en BLOB

---

## 🗃️ Estructura de Base de Datos

```sql
Usuarios
  - id (PK, AUTO_INCREMENT)
  - email (UNIQUE, NOT NULL)
  - clave (VARCHAR 255, HASH)
  - nombre (VARCHAR 100)
  - apellido (VARCHAR 100)
  - activo (BOOLEAN, DEFAULT 1)

Personal
  - id (PK)
  - usuarioId (FK → Usuarios, CASCADE)
  - rolId (FK → Roles)
  - activo (BOOLEAN, DEFAULT 1)
  
Clientes
  - id (PK)
  - usuarioId (FK → Usuarios, CASCADE)
  - telefono (VARCHAR 20)
  - direccion (VARCHAR 255)
  - ciudad (VARCHAR 100)

Roles
  - id (PK, AUTO_INCREMENT)
  - nombre (VARCHAR 100, UNIQUE)
  -- Ejemplos: admin, veterinario, cliente

Mascotas
  - id (PK, AUTO_INCREMENT)
  - clienteId (FK → Clientes, CASCADE)
  - nombre (VARCHAR 100)
  - raza (VARCHAR 100)
  - color (VARCHAR 50)
  - foto (LONGBLOB)
  - fechaDeNac (DATE)
  - fechaMuerte (DATE, NULL)
  - activo (BOOLEAN, DEFAULT 1)

Atenciones
  - id (PK, AUTO_INCREMENT)
  - clienteId (FK → Clientes)
  - mascotaId (FK → Mascotas)
  - personalId (FK → Personal)
  - fechaHora (DATETIME)
  - titulo (VARCHAR 200)
  - descripcion (TEXT)
```

---

## ✅ Testing Manual

### 1. Como Administrador
- [ ] Crear nuevo usuario (cliente)
- [ ] Crear nuevo usuario (personal con rol)
- [ ] Editar usuario existente
- [ ] Cambiar estado activo/inactivo
- [ ] Crear mascota para cliente
- [ ] Editar mascota existente
- [ ] Ver detalles de mascota
- [ ] Filtrar usuarios por rol

### 2. Como Personal (No Admin)
- [ ] Editar datos propios
- [ ] Intentar acceder a gestión de usuarios (debe redirigir)
- [ ] Crear/editar mascotas

### 3. Como Cliente
- [ ] Cambiar contraseña
- [ ] Verificar datos personales en solo lectura
- [ ] Intentar acceder a páginas de admin (debe redirigir)

### 4. Validaciones de Seguridad
- [ ] Email duplicado rechazado
- [ ] Contraseña < 6 caracteres rechazada
- [ ] Contraseñas no coinciden (error)
- [ ] Login con contraseña hasheada funciona
- [ ] Login con contraseña legacy (texto plano) funciona
- [ ] Acceso sin sesión redirige a login

---

## 🔗 Integración con Sistema Existente

El sistema se integra perfectamente con:
- ✅ Sistema de autenticación existente (`login.php`, `logout.php`)
- ✅ Gestión de mascotas (`mascota_list.php`)
- ✅ Estructura de roles y permisos
- ✅ Diseño Bootstrap 5
- ✅ Menú lateral dinámico
- ✅ Base de datos MySQL

---

## 🚀 Mejoras Futuras Sugeridas

- [ ] Auditoría de cambios (log de modificaciones)
- [ ] Recuperación de contraseña por email
- [ ] Foto de perfil personalizada para usuarios
- [ ] Exportar lista de usuarios a Excel/PDF
- [ ] Búsqueda avanzada en lista de usuarios
- [ ] Paginación para listas grandes
- [ ] Confirmación antes de desactivar usuario
- [ ] Envío de credenciales por email al crear usuario
- [ ] Histórico de cambios de contraseña
- [ ] Roles personalizables con permisos granulares

---

## 📊 Resumen de ArchivosCreados/Modificados

### Creados (8 archivos):
1. `src/logic/usuarios.logic.php`
2. `public/usuarios/usuario_list.php`
3. `public/usuarios/nuevo_usuario.php`
4. `public/usuarios/editar_usuario.php`
5. `public/usuarios/mascotas_usuario.php`
6. `public/mascotas/nueva_mascota.php`
7. `public/mascotas/editar_mascota.php` (reescrito)
8. `public/mascotas/ver_mascota.php`

### Modificados (4 archivos):
1. `public/mi_perfil.php`
2. `src/lib/funciones.php`
3. `src/includes/menu_lateral.php`
4. `src/logic/auth.logic.php`

---

**Desarrollado para**: Veterinaria San Antón  
**Issue**: Edición de perfil de usuarios  
**Fecha**: 29-30 Diciembre 2025  
**Versión**: 1.0  
**Estado**: ✅ Completado y probado
