# Sistema de Gestión de Usuarios - Veterinaria San Antón

## Funcionalidad Implementada

Sistema completo de edición de perfiles de usuarios con permisos diferenciados según el rol del usuario.

## Características Principales

### 🔐 Tres Niveles de Permisos

1. **Administrador/Secretaría**
   - ✅ Edición completa de cualquier usuario
   - ✅ Gestión de mascotas de clientes
   - ✅ Control de estado activo/inactivo de usuarios
   - ✅ Módulo dedicado de gestión de usuarios

2. **Personal**
   - ✅ Edición de datos propios (email, nombre, apellido)
   - ❌ Sin acceso a otros usuarios
   - ❌ Sin acceso a mascotas

3. **Cliente**
   - ✅ Cambio de contraseña
   - ❌ Datos personales en solo lectura (editados por admin)

## Archivos del Sistema

### Lógica de Negocio
- **`src/logic/usuarios.logic.php`** - Funciones CRUD de usuarios
- **`src/lib/funciones.php`** - Funciones auxiliares (ampliado)

### Interfaces de Usuario
- **`public/usuarios/usuario_list.php`** - Listado de usuarios (admin)
- **`public/usuarios/editar_usuario.php`** - Edición de usuario (admin)
- **`public/usuarios/mascotas_usuario.php`** - Mascotas del cliente (admin)
- **`public/mi_perfil.php`** - Perfil propio (todos los usuarios) - MODIFICADO

### Componentes
- **`src/includes/menu_lateral.php`** - Menú lateral (agregado enlace Usuarios) - MODIFICADO

## Guía de Uso

### Para Administradores

**Gestionar Usuarios:**
1. Ir al menú lateral → "USUARIOS"
2. Ver lista completa con filtros (Todos/Clientes/Personal)
3. Hacer clic en el botón de editar (lápiz) para modificar datos
4. Editar: email, nombre, apellido, estado activo
5. Para clientes: también editar teléfono, ciudad, dirección
6. Guardar cambios

**Gestionar Mascotas de un Cliente:**
1. En la lista de usuarios, hacer clic en el icono de mascota (pata)
2. Ver todas las mascotas del cliente
3. Editar o ver detalles de cada mascota
4. Crear nueva mascota para el cliente

### Para Personal

**Editar Perfil Propio:**
1. Ir a "Mi Perfil" en el menú de cuenta (esquina superior derecha)
2. Modificar: nombre, apellido, email
3. Guardar cambios

### Para Clientes

**Cambiar Contraseña:**
1. Ir a "Mi Perfil" en el menú de cuenta
2. Ingresar contraseña actual
3. Ingresar nueva contraseña (mínimo 6 caracteres)
4. Confirmar nueva contraseña
5. Guardar cambios

*Nota: Los clientes verán sus datos personales en modo lectura. Para cambiarlos, deben contactar al administrador.*

## Validaciones de Seguridad

- ✅ Autenticación requerida en todas las páginas
- ✅ Validación de roles y permisos
- ✅ Prevención de SQL Injection (prepared statements)
- ✅ Email único (no permite duplicados)
- ✅ Contraseñas hasheadas con bcrypt
- ✅ Validación de contraseña actual antes de cambiar
- ✅ Longitud mínima de contraseña: 6 caracteres
- ✅ Redirección automática si no tiene permisos

## Estructura de Base de Datos

El sistema utiliza las siguientes tablas:

```sql
Usuarios
  - id (PK)
  - email (UNIQUE)
  - clave (PASSWORD HASH)
  - nombre
  - apellido
  - activo

Personal
  - id (PK)
  - usuarioId (FK → Usuarios)
  - rolId (FK → Roles)
  
Clientes
  - id (PK)
  - usuarioId (FK → Usuarios)
  - telefono
  - direccion
  - ciudad

Roles
  - id (PK)
  - nombre (admin, veterinario, etc.)

Mascotas
  - id (PK)
  - clienteId (FK → Clientes)
  - nombre
  - raza
  - color
  - foto
  - fechaDeNac
  - fechaMuerte
  - activo
```

## Testing Manual

Para probar el sistema, se recomienda:

1. **Como Admin**: 
   - Editar usuarios de diferentes tipos
   - Cambiar estados activo/inactivo
   - Editar mascotas de clientes

2. **Como Personal**:
   - Editar datos propios
   - Intentar acceder a `/usuarios/usuario_list.php` (debería redirigir)

3. **Como Cliente**:
   - Cambiar contraseña
   - Verificar que datos personales están en solo lectura
   - Intentar acceder a páginas de admin (debería redirigir)

## Integración con Sistema Existente

El sistema se integra perfectamente con:
- ✅ Sistema de autenticación existente
- ✅ Gestión de mascotas
- ✅ Estructura de roles y permisos
- ✅ Diseño Bootstrap del sitio
- ✅ Menú lateral dinámico

## Próximas Posibles Mejoras

- [ ] Auditoría de cambios (log de quién editó qué)
- [ ] Recuperación de contraseña por email
- [ ] Foto de perfil personalizada
- [ ] Exportar lista de usuarios a Excel/PDF
- [ ] Búsqueda y ordenamiento en la lista de usuarios
- [ ] Paginación para listas grandes
- [ ] Confirmación antes de desactivar usuario

---

**Desarrollado para**: Veterinaria San Antón  
**Fecha**: Diciembre 2025  
**Versión**: 1.0
