# Sistema de Bajas Lógicas - Veterinaria San Antón

## 📋 Descripción de la Tarea

Implementación de un sistema de **bajas lógicas** utilizando el campo `activo` en las entidades principales. El objetivo es evitar el borrado físico de datos, permitiendo desactivar registros sin perder la integridad referencial ni el histórico.

---

## 🛠️ Implementación Técnica

### 1. Nivel de Base de Datos

Se utiliza la columna `activo` (BOOLEAN/TINYINT) en las siguientes tablas:

- `Usuarios`: Controla el acceso al sistema.
- `Personal`: Vinculado a usuarios del personal.
- `Mascotas`: Controla la visibilidad de las mascotas.
- `Productos`: Controla la disponibilidad en el catálogo.
- `Atenciones`: Controla la visibilidad de las atenciones.
- `Servicios`: Controla la disponibilidad de los servicios.

### 2. Lógica de Consultas (Backend)

Se han modificado las funciones core en `src/lib/funciones.php` y `src/logic/usuarios.logic.php` para filtrar por defecto los registros activos:

- **Usuarios**: `get_all_usuarios($mostrar_inactivos = false)`
  - **Desmarcado (OFF)**: Devuelve **TODOS** los usuarios.
  - **Marcado (ON)**: Devuelve **exclusivamente** registros con `activo = 0`.
- **Mascotas**: `get_all_mascotas($mostrar_inactivas = false)` (Comportamiento idéntico a Usuarios).
- **Atenciones**:
  - `get_all_atenciones($mostrar_inactivas)` y `get_atenciones_by_fecha($fecha, $mostrar_inactivas)`.
  - Se han unificado los nombres de columnas y agregado alias para compatibilidad con la UI (`fechaHora as fecha`, `titulo as motivo`).
- **Servicios**: `get_all_servicios($mostrar_inactivos)`.
- **Catálogo**: Ya filtraba por `activo = 1`.

### 3. Nuevas Funcionalidades de Gestión

Se han creado/actualizado archivos de lógica para manejar las bajas y reactivaciones:

- **Usuarios** (`usuarios.logic.php`): `dar_baja_usuario()`, `reactivar_usuario()`.
- **Mascotas** (`mascotas.logic.php`): `dar_baja_mascota()`, `reactivar_mascota()`, `registrar_fallecimiento_mascota()`.
- **Atenciones** (`atenciones.logic.php`): `dar_baja_atencion()`, `reactivar_atencion()`.
- **Servicios** (`servicios.logic.php`): `dar_baja_servicio()`, `reactivar_servicio()`.

### 4. Cambios en la Base de Datos

Para soportar estas funcionalidades, se han realizado las siguientes alteraciones:

- `ALTER TABLE atenciones ADD COLUMN activo TINYINT(1) DEFAULT 1`
- `ALTER TABLE atenciones ADD COLUMN estado VARCHAR(50) DEFAULT 'pendiente'` (Para consistencia con la UI).
- `ALTER TABLE servicios ADD COLUMN activo TINYINT(1) DEFAULT 1`

---

## 🖥️ Interfaz de Usuario (UI)

### Gestión de Usuarios

- **Listado**: Se agregó un switch **"Ver inactivos"** para administradores.
- **Badges**: Los usuarios inactivos se muestran con una etiqueta gris "Inactivo".
- **Edición**: Se permite activar/desactivar la cuenta mediante un checkbox de estado.

### Gestión de Mascotas

- **Listado General**: Solo muestra mascotas activas.
- **Mis Mascotas (Clientes)**: Solo muestra mascotas activas.
- **Edición**: Próximamente se integrarán botones de baja rápida.

---

## 🔒 Seguridad e Integridad

- **Acceso Directo**: Aunque un registro sea "dado de baja", su ID sigue existiendo en la DB, manteniendo la integridad con Atenciones y otros registros históricos.
- **Login**: El sistema de autenticación (`auth.logic.php`) ha sido verificado para asegurar que usuarios con `activo = 0` no puedan iniciar sesión aunque su contraseña sea correcta.

---

## ✅ Cómo Probar las Bajas Lógicas

1. **Usuarios**:

   - Vaya a Gestión de Usuarios.
   - Desactive un usuario cliente.
   - Verifique que desaparece de la lista.
   - Active "Ver inactivos" y verifique que reaparece con badge gris.
   - Intente loguearse con esa cuenta (debe fallar).

2. **Mascotas**:
   - Vaya a la edición de una mascota.
   - Márquela como inactiva (o registre fallecimiento).
   - Verifique que el cliente ya no la ve en "Mis Mascotas".
   - Verifique que el personal no la ve en el listado general (salvo edición directa).
