# Documentación: ABM Novedades y Catálogos

## Descripción General

Se han implementado dos nuevos módulos ABM (Alta, Baja, Modificación) para la gestión de **Novedades** y **Catálogos** en el sistema de la veterinaria.

### Características Principales

- **Acceso público para visualización**: Cualquier persona puede ver novedades y catálogos sin necesidad de iniciar sesión
- **Modificación solo para administradores**: Solo usuarios con rol admin pueden crear, editar y eliminar
- **Imágenes opcionales**: Soporte para subir imágenes en ambos módulos
- **Modal para detalles**: Vista expandida sin cambiar de página
- **Filtrado y búsqueda**: En catálogos, filtrado por categoría y búsqueda por nombre
- **Validaciones completas**: Cliente y servidor

---

## Estructura de Base de Datos

### Tabla `novedades`

```sql
- id (int, PK, auto_increment)
- titulo (varchar 200, NOT NULL)
- contenido (text, NOT NULL)
- imagen (varchar 255, nullable)
- fechaPublicacion (datetime, default CURRENT_TIMESTAMP)
- usuarioId (int, FK -> usuarios.id)
```

### Tabla `catalogos`

```sql
- id (int, PK, auto_increment)
- nombre (varchar 200, NOT NULL)
- descripcion (text, nullable)
- precio (decimal 10,2, nullable)
- imagen (varchar 255, nullable)
- categoria (varchar 100, nullable, indexed)
- stock (int, default 0)
- usuarioId (int, FK -> usuarios.id)
```

---

## Archivos Creados

### Backend - Lógica de Negocio

#### `src/logic/novedades.logic.php`

Funciones disponibles:

- `obtenerNovedades()` - Lista todas las novedades con datos del autor
- `obtenerNovedadPorId($id)` - Obtiene una novedad específica
- `crearNovedad($titulo, $contenido, $imagen, $usuarioId)` - Crea nueva novedad
- `modificarNovedad($id, $titulo, $contenido, $imagen)` - Actualiza novedad
- `eliminarNovedad($id)` - Elimina novedad (y su imagen)
- `subirImagenNovedad($archivo)` - Maneja la subida de imágenes

#### `src/logic/catalogos.logic.php`

Funciones disponibles:

- `obtenerCatalogos($categoria, $busqueda)` - Lista productos con filtros opcionales
- `obtenerCategorias()` - Obtiene categorías únicas
- `obtenerCatalogoPorId($id)` - Obtiene producto específico
- `crearCatalogo($nombre, $descripcion, $precio, $imagen, $categoria, $stock, $usuarioId)` - Crea producto
- `modificarCatalogo($id, ...)` - Actualiza producto
- `eliminarCatalogo($id)` - Elimina producto (y su imagen)
- `subirImagenCatalogo($archivo)` - Maneja la subida de imágenes

### Frontend - Vistas

#### Módulo Novedades

- **`public/novedad_list.php`**: Lista de novedades con modal de detalle
- **`public/novedades/crear.php`**: Formulario de creación (solo admin)
- **`public/novedades/editar.php`**: Formulario de edición (solo admin)
- **`public/novedades/eliminar.php`**: Handler de eliminación (solo admin)

#### Módulo Catálogos

- **`public/catalogo_list.php`**: Lista de productos con filtros y modal
- **`public/catalogos/crear.php`**: Formulario de creación (solo admin)
- **`public/catalogos/editar.php`**: Formulario de edición (solo admin)
- **`public/catalogos/eliminar.php`**: Handler de eliminación (solo admin)

---

## Uso del Sistema

### Para Administradores

**Gestionar Novedades:**

1. Acceder a "Novedades" desde el menú
2. Click en "+ Nueva Novedad"
3. Completar título y contenido (obligatorios)
4. Opcionalmente agregar imagen (JPG, PNG, GIF, máx 5MB)
5. Para editar: click en "Editar" en el card de la novedad
6. Para eliminar: click en "Eliminar" (requiere confirmación)

**Gestionar Catálogo:**

1. Acceder a "Catálogo" desde el menú
2. Click en "+ Nuevo Producto"
3. Completar nombre (obligatorio), precio, stock, descripción
4. Seleccionar categoría existente o crear nueva
5. Opcionalmente agregar imagen
6. Para editar/eliminar: botones en el card del producto

### Para Usuarios (Clientes, Veterinarios, Peluqueros)

**Ver Novedades:**

1. Acceder a "Novedades"
2. Ver lista de novedades publicadas
3. Click en "Ver más" para leer completo en modal

**Ver Catálogo:**

1. Acceder a "Catálogo"
2. Usar filtros de categoría o búsqueda
3. Click en "Ver detalles" para información completa

---

## Validaciones y Seguridad

### Backend

- Verificación de roles para operaciones de administración
- Prepared statements para prevenir SQL injection
- Sanitización de entradas de usuario
- Validación de tipos de archivo (solo imágenes)
- Límite de tamaño de archivo (5MB)
- Eliminación automática de imágenes al eliminar registros

### Frontend

- Campos obligatorios marcados con (\*)
- Validación HTML5 en formularios
- Confirmación antes de eliminar
- Ocultamiento de botones admin para usuarios sin permisos

---

## Directorios de Imágenes

Las imágenes se almacenan en:

- **Novedades**: `public/uploads/novedades/`
- **Catálogos**: `public/uploads/catalogos/`

Los directorios se crean automáticamente si no existen.

---

## Pruebas Recomendadas

### Como Admin

1. ✓ Crear novedad sin imagen
2. ✓ Crear novedad con imagen
3. ✓ Editar novedad y cambiar imagen
4. ✓ Eliminar novedad (verificar eliminación de imagen)
5. ✓ Crear producto con todos los campos
6. ✓ Filtrar productos por categoría
7. ✓ Buscar productos por nombre
8. ✓ Editar producto y actualizar stock
9. ✓ Eliminar producto

### Como Usuario No-Admin

1. ✓ Verificar visualización de novedades
2. ✓ Verificar que no aparecen botones de administración
3. ✓ Intentar acceso directo a URLs de creación (debe redirigir)
4. ✓ Ver detalles en modal
5. ✓ Filtrar y buscar en catálogo

---

## Notas Técnicas

- Los campos de precio usan `decimal(10,2)` para precisión monetaria
- Las fechas se formatean en español (dd/mm/yyyy)
- Los modales usan JavaScript vanilla (sin dependencias)
- Las imágenes tienen nombres únicos (uniqid + timestamp)
- Los formularios usan `enctype="multipart/form-data"` para subida de archivos

---

## Actualización de Base de Datos

Para aplicar los cambios en la base de datos:

```bash
# Opción 1: Reiniciar desde cero (elimina datos existentes)
mysql -u root veterinaria_db < init.sql

# Opción 2: Ejecutar solo las nuevas tablas (preserva datos)
# Copiar manualmente las secciones de CREATE TABLE y ALTER TABLE
# de init.sql y ejecutarlas en phpMyAdmin o cliente MySQL
```

**IMPORTANTE**: Hacer backup de la base de datos antes de aplicar cambios.
