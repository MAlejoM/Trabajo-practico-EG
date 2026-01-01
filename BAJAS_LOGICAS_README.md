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

Se han modificado las funciones core en `src/lib/funciones.php` y `src/logic/usuarios.logic.php` para filtrar por defecto solo los registros activos:

- **Usuarios**: `get_all_usuarios($mostrar_inactivos = false)`
  - **Desmarcado (OFF)**: Devuelve **SOLO** usuarios activos (`activo = 1`).
  - **Marcado (ON)**: Devuelve **TODOS** los usuarios (activos e inactivos).
- **Mascotas**: `get_all_mascotas($mostrar_inactivas = false)` (Comportamiento idéntico a Usuarios).
- **Atenciones**:
  - `get_all_atenciones($mostrar_inactivas)` y `get_atenciones_by_fecha($fecha, $mostrar_inactivas)`.
  - Se han unificado los nombres de columnas y agregado alias para compatibilidad con la UI (`fechaHora as fecha`, `titulo as motivo`).
  - Por defecto filtra solo atenciones activas.
- **Servicios**: `get_all_servicios($mostrar_inactivos)`.
  - Por defecto filtra solo servicios activos.
- **Catálogo**: Ya filtraba por `activo = 1`.
- **Funciones específicas**: `get_mascotas_by_cliente_id()`, `get_all_clientes()`, y `get_cliente_completo_by_id()` siempre filtran por `activo = 1`.
- **Catálogo**: Ya filtraba por `activo = 1`.

### 3. Nuevas Funcionalidades de Gestión

Se han creado/actualizado archivos de lógica para manejar las bajas y reactivaciones:

- **Usuarios** (`usuarios.logic.php`): `dar_baja_usuario()`, `reactivar_usuario()`.
- **Mascotas** (`mascotas.logic.php`): `dar_baja_mascota()`, `reactivar_mascota()`, `registrar_fallecimiento_mascota()`.
- **Atenciones** (`atenciones.logic.php`): `dar_baja_atencion()`, `reactivar_atencion()`.
- **Servicios** (`servicios.logic.php`): `dar_baja_servicio()`, `reactivar_servicio()`.

### 4. Cambios en la Base de Datos

Para soportar estas funcionalidades, se han realizado las siguientes modificaciones en `init.sql`:

- Tabla `atenciones`: Se agregaron las columnas `activo TINYINT(1) DEFAULT 1` y `estado VARCHAR(50) DEFAULT 'pendiente'`
- Tabla `servicios`: Ya contenía la columna `activo TINYINT(1) DEFAULT 1`
- Tablas `usuarios`, `mascotas`, `productos`, `personal`, `novedades`: Ya contenían el campo `activo`

---

## 🖥️ Interfaz de Usuario (UI)

### Gestión de Usuarios

- **Listado**: Se agregó un toggle **"Ver todos (incluir inactivos)"** para administradores.
- **Comportamiento**: Por defecto muestra solo usuarios activos. Al activar el toggle, muestra todos (activos e inactivos).
- **Badges**: Los usuarios inactivos se muestran con una etiqueta gris "Inactivo".
- **Edición**: Se permite activar/desactivar la cuenta mediante un checkbox de estado.

### Gestión de Mascotas

- **Listado General**: Por defecto muestra solo mascotas activas. El toggle "Ver todas (incluir inactivas)" permite ver todas.
- **Mis Mascotas (Clientes)**: Solo muestra mascotas activas (sin opción de ver inactivas).
- **Edición**: Se pueden gestionar bajas y reactivaciones desde la página de edición.

### Gestión de Atenciones

- **Listados**: Por defecto muestran solo atenciones activas. El toggle permite ver todas.
- **Integración completa**: Tanto en vista general como en vista por fecha.

---

## 🔒 Seguridad e Integridad

- **Acceso Directo**: Aunque un registro sea "dado de baja", su ID sigue existiendo en la DB, manteniendo la integridad con Atenciones y otros registros históricos.
- **Login**: El sistema de autenticación (`auth.logic.php`) ha sido verificado para asegurar que usuarios con `activo = 0` no puedan iniciar sesión aunque su contraseña sea correcta.

---

## ✅ Cómo Probar las Bajas Lógicas

1. **Usuarios**:

   - Vaya a Gestión de Usuarios.
   - Por defecto solo verá usuarios activos.
   - Desactive un usuario cliente desde su página de edición.
   - Verifique que desaparece del listado.
   - Active el toggle "Ver todos (incluir inactivos)" y verifique que reaparece con badge gris "Inactivo".
   - Intente loguearse con esa cuenta (debe fallar).

2. **Mascotas**:

   - Vaya a la edición de una mascota.
   - Márquela como inactiva (o registre fallecimiento).
   - Verifique que desaparece del listado general (que por defecto solo muestra activas).
   - Active el toggle "Ver todas (incluir inactivas)" para verificar que sigue existiendo.
   - Verifique que el cliente ya no la ve en "Mis Mascotas".

3. **Atenciones**:

   - Las atenciones pueden marcarse como inactivas (bajas lógicas).
   - Por defecto los listados solo muestran atenciones activas.
   - Use el toggle para ver todas las atenciones incluyendo las inactivas.
