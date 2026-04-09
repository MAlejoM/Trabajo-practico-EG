# CHECKLISTS

## TEST DE USABILIDAD

### Identidad Corporativa

**¿La portada del Sitio refleja la identidad y pertenencia de la institución?**
Sí, porque está explícito el nombre del sistema "Veterinaria San Antón" junto con el logotipo, y la página principal muestra claramente que se trata de un sistema de gestión veterinaria orientado al cuidado de mascotas.

**¿Existen elementos de la imagen corporativa en la Portada de su Sitio? ¿Se repiten en todas las páginas?**
Sí, el logotipo, el nombre "Veterinaria San Antón" y la paleta de colores corporativa (verde Bootstrap success `#198754`) se repiten en todas las páginas a través del navbar y el footer.

**¿El logotipo ha sido incluido en un lugar importante en la Portada y en las páginas interiores del Sitio?**
Sí, el logotipo (`Logo.jpeg`) aparece en la barra de navegación superior de todas las páginas junto al nombre "Veterinaria San Antón", y también se muestra de forma destacada en la pantalla de inicio de sesión.

**¿Todas las páginas cuentan con un título que indique el nombre de la institución e información de contactos virtuales y físicos al pie de la página?**
Sí, todas las páginas incluyen el título "Veterinaria San Antón" en el navbar y el footer contiene el copyright con el nombre de la institución, además de enlaces directos a la página de Contacto y a "¿Quiénes somos?".

---

### Utilidad del Sitio Web

**¿El Sitio ofrece información sobre las actividades y servicios más recientes e importantes que está llevando a cabo la institución?**
Sí, la sección de Novedades permite publicar y visualizar las últimas noticias y actividades de la veterinaria. Además, la sección de Catálogo muestra los productos y servicios disponibles.

**¿Los usuarios pueden encontrar fácilmente en la portada la información acerca de las actividades y servicios más importantes de la institución?**
Sí, la página principal para usuarios no autenticados presenta una sección hero de bienvenida con botones de acceso directo a "Ver catálogo" y "Últimas novedades". Para usuarios autenticados, el dashboard muestra tarjetas de acceso rápido a todas las secciones según su rol.

---

### Navegación

**¿El diseño del Sitio es eficiente, rápido e intuitivo?**
Sí, el sitio es intuitivo y fácil de navegar. Utiliza un diseño basado en tarjetas con iconos representativos, un menú lateral con opciones claras y todas las páginas tienen salidas de escape a la página anterior o a la página principal.

**¿Aparece el menú de navegación en un lugar destacado? ¿Se ve fácilmente?**
Sí, el menú de navegación se encuentra en la barra superior (navbar) y se complementa con un menú lateral visible en todas las secciones interiores. Es completamente responsivo y se adapta a dispositivos móviles mediante un menú colapsable.

**¿Verificó la consistencia de todos los enlaces?**
Sí, se verificó la consistencia de todos los enlaces y funcionan correctamente.

**¿El Sitio cuenta con un mapa o buscador que facilite el acceso directo a los contenidos?**
No cuenta con un mapa de sitio ni un buscador global. Sin embargo, la navegación por tarjetas en el dashboard y el menú lateral permiten acceder directamente a cualquier sección en pocos clics.

**¿El Sitio mantiene una navegación consistente y coherente en todas las pantallas?**
Sí, se mantiene una navegación consistente en todas las pantallas gracias al uso de templates compartidos para el header, footer y menú lateral.

---

### Visibilidad del estado del sistema

**¿Se informa al usuario claramente el área del Sitio que está visitando?**
Sí, todas las páginas tienen títulos claros (headings `<h2>` o `<h1>`) que indican la sección que se está visitando, como "Mascotas", "Novedades", "Usuarios", etc.

**¿El Sitio Web diferencia entre enlaces visitados y enlaces por visitar?**
No, no se implementó una diferenciación visual entre enlaces visitados y no visitados en la hoja de estilos personalizada. Se utiliza la estilización por defecto de Bootstrap.

**En caso de servicios o trámites en línea, ¿ofrece información de cuántos pasos faltan para terminar?**
No aplica, ya que el sitio no cuenta con trámites en línea de múltiples pasos. Los formularios de carga son de una sola pantalla.

---

### Consistencia y cumplimiento de estándares

**¿El HTML del Sitio ha sido validado satisfactoriamente según w3c.org?**
Sí, se validó satisfactoriamente el HTML según el validador de w3c.org.

**¿El o los archivos de Hojas de estilo (CSS) han sido aprobados según w3c.org?**
Sí, a excepción de los errores generados por los archivos internos de Bootstrap, que no pueden modificarse sin afectar el funcionamiento del framework. El CSS personalizado del sitio pasa la validación sin inconvenientes.

**¿Comprobó la consistencia de Links usando el verificador de w3c.org?**
Sí, se comprobaron los enlaces y son consistentes.

---

### Atención de errores

**¿Usa Javascript para validar formularios durante su llenado y antes de enviarlos?**
Sí, se utiliza validación HTML5 nativa (atributos `required`, `minlength`, `type="email"`, etc.) y JavaScript para funcionalidades dinámicas como búsqueda AJAX de mascotas con debounce, selección dinámica de servicios según el profesional, y visualización condicional de campos según el tipo de usuario.

**¿Usa elementos destacados para indicar los campos obligatorios dentro de un formulario?**
Sí, se utilizan asteriscos (`*`) junto a las etiquetas de los campos obligatorios en todos los formularios del sitio.

**¿Después de que ocurre un error, es fácil volver a la página donde se encontraba antes que se produjese o entrega recomendaciones de los pasos a seguir?**
Sí, cuando se produce un error se muestra un mensaje flash informativo (alerta dismissible de Bootstrap) y se recarga la misma página o se redirige a la página correspondiente, permitiendo al usuario corregir la acción.

---

### Estética y diseño

**¿Usa jerarquías visuales para determinar lo importante con una sola mirada?**
Sí, se utilizan jerarquías visuales claras. Los títulos están bien diferenciados del contenido, las tarjetas del dashboard usan iconos de colores distintivos para cada sección, y los botones de acción principal se destacan con el color corporativo verde.

**¿Las imágenes tienen tamaños adecuados que no dificultan el acceso a las páginas?**
Sí, las imágenes tienen tamaños adecuados y se utiliza `object-fit: cover` para mantener proporciones correctas sin distorsionar el diseño. La imagen hero de bienvenida tiene un `max-height` de 340px.

**¿Las imágenes tienen etiqueta ALT en el código HTML para facilitar la navegación?**
Sí, todas las imágenes contienen la propiedad `alt` en el código HTML. Por ejemplo, las imágenes de mascotas utilizan `alt` con el nombre de la mascota correspondiente, y el logo utiliza `alt="Logo"`.

---

### Ayuda ante errores

**En caso de errores de consistencia dentro del sitio, ¿se ofrece un mensaje personalizado mediante una página explicativa? (Por ejemplo: Error 404 para página inexistente)**
Sí, el sitio cuenta con una página personalizada de Error 404 que muestra un ícono animado de huella de mascota, el mensaje "¡Oops! Página no encontrada" con una descripción amigable ("Parece que el rastro que buscabas se ha perdido...") y botones para volver al inicio o regresar a la página anterior.

**¿Entrega información de contacto fuera de Internet? (Por ejemplo: teléfono institucional)**
Sí, la página de Contacto incluye dirección física (Calle Falsa 123, Ciudad), teléfono (+54 11 1234-5678), correo electrónico (contacto@veterinariasananton.com) y horarios de atención (Lunes a Viernes 9:00-18:00 hs, Sábados 9:00-13:00 hs).

**¿Ofrece área de Preguntas Frecuentes con datos de ayuda a usuarios?**
No, actualmente el sitio no cuenta con una sección de Preguntas Frecuentes.

**¿Ofrece páginas de ayuda que explican cómo usar el Sitio?**
No, el sitio no ofrece páginas de ayuda explícitas. Sin embargo, la interfaz es suficientemente intuitiva y los formularios incluyen indicaciones claras (placeholders, etiquetas descriptivas y asteriscos en campos obligatorios) para guiar al usuario.

---

### Retroalimentación (Feedback)

**¿Puede el usuario ponerse en contacto con el encargado del Sitio Web para hacer sugerencias o comentarios?**
Sí, en la sección de Contacto se proporcionan los medios para comunicarse con la veterinaria (teléfono, email, dirección). El acceso a esta sección está disponible desde el footer de todas las páginas.

**¿Funcionan correctamente los formularios de contacto? ¿Ha probado cada uno de ellos?**
El sitio utiliza un sistema de correo configurado con PHPMailer a través de SMTP (Gmail). Se probó y funciona correctamente el envío de correos, incluyendo la funcionalidad de recuperación de contraseña.

**¿Hay alguien encargado de recibir y contestar estos mensajes?**
Sí, los mensajes se dirigen al correo de contacto de la veterinaria y son gestionados por el administrador del sitio.

---

## TEST DE ACCESIBILIDAD

**¿Se proporciona un texto equivalente para todo elemento no textual, tales como imágenes, para explicar su contenido a discapacitados visuales?**
Sí, las imágenes del sitio cuentan con atributos `alt` descriptivos. Los íconos se implementan mediante Font Awesome (`<i class="fas fa-*">`), que son decorativos y no requieren texto alternativo adicional.

**¿La información transmitida a través de los colores también está disponible sin color?**
Sí, sin una hoja de estilo el contenido se puede leer sin problemas. Los elementos visuales como íconos y etiquetas de texto complementan la información transmitida por colores.

**¿El documento está estructurado para que pueda ser leído con o sin una hoja de estilo, utilizando adecuadamente los tags de HTML?**
Sí, se utilizan etiquetas semánticas de HTML5 como `<nav>`, `<main>`, `<footer>`, `<form>`, `<label>`, `<h1>`-`<h5>`, y elementos `aria-label` en la paginación, lo que permite una lectura correcta sin hoja de estilo.

**¿El documento está escrito en un lenguaje adecuado y se deja claro cuando se cambia de idioma?**
Sí, el sitio está escrito en español. No tiene traducción a otros idiomas ya que está orientado al público argentino.

**¿Las tablas se utilizan para presentar información y no para diagramar el contenido del Sitio Web?**
Sí, las tablas se utilizan exclusivamente para presentar información de listados (usuarios, mascotas, atenciones, etc.) y no para maquetar el diseño del sitio, el cual se basa en el sistema de grillas de Bootstrap.

**¿Las páginas que utilizan nuevas tecnologías siguen funcionando cuando dicha tecnología no está presente (por ejemplo, los plug-ins de Flash)?**
Sí, el sitio funciona normalmente sin plug-ins externos. Utiliza tecnologías estándar: HTML5, CSS3, JavaScript y Bootstrap.

**¿Es posible controlar los objetos o las páginas que se actualizan o se cambian automáticamente, permitiendo incluso generar pausas para su revisión?**
Sí, es posible. El sitio no contiene elementos de actualización automática que impidan la revisión del contenido.

**¿Se asegura la accesibilidad de los elementos de la página que tengan sus propias interfaces?**
Sí, se utilizan componentes accesibles de Bootstrap (modales con roles ARIA, menús colapsables, alertas dismissibles) que cuentan con soporte de accesibilidad incorporado.

**¿Se permite al usuario activar elementos de las páginas, usando cualquier dispositivo como el mouse o el teclado y no sólo uno en particular?**
Sí, se permite. Los elementos interactivos (botones, enlaces, formularios) son accesibles tanto con mouse como con teclado gracias a las implementaciones estándar de HTML y Bootstrap.

**¿Se usan las tecnologías y guías de trabajo generadas por la W3C?**
Sí, se utilizan estándares HTML5 y CSS3 validados por W3C, además de atributos ARIA donde corresponde.

**¿Se ofrece ayuda y orientación a los usuarios para entender páginas o elementos complejos dentro de ellas?**
Sí, los formularios incluyen placeholders descriptivos, etiquetas claras y la interfaz de tarjetas con íconos facilita la comprensión de cada sección.

**¿Se ofrecen elementos de navegación claros?**
Sí, se ofrecen elementos de navegación claros a través del navbar superior, el menú lateral con botones descriptivos y las tarjetas de acceso rápido en el dashboard.

**¿Se asegura que los documentos que se ofrecen a través del Sitio son simples, claros y pueden ser fácilmente entendidos?**
Sí, el sitio es simple, claro y fácilmente entendible. La interfaz utiliza un diseño minimalista con tarjetas, íconos y colores para facilitar la comprensión.

---

## RAPIDEZ DE ACCESO

**¿El usuario puede encontrar en no más de 3 clics la información buscada?**
Sí, la estructura del sitio está organizada de manera que cualquier sección se alcanza en un máximo de 2-3 clics desde la página principal, gracias al dashboard de tarjetas y el menú lateral.

**¿Aparece el menú de navegación en un lugar destacado? ¿Se ve fácilmente?**
Sí, el menú de navegación está en la parte superior de todas las páginas (navbar fijo) y se complementa con un menú lateral visible en las secciones interiores.

**¿El Sitio cuenta con un mapa y/o buscador que dé un acceso alternativo a los contenidos?**
No cuenta con mapa de sitio ni buscador global. El acceso se realiza mediante el menú de navegación y las tarjetas del dashboard.

**¿Es fácil llegar a las secciones más importantes del Sitio desde cualquier página?**
Sí, es fácil. El navbar superior y el menú lateral están presentes en todas las páginas, permitiendo acceso directo a cualquier sección.

**¿El Sitio mantiene una navegación consistente y coherente en todas sus páginas?**
Sí, se mantiene una navegación consistente en todas las páginas gracias al uso de templates compartidos (header, footer, menú lateral).

**¿El diseño usa jerarquías visuales para determinar lo importante con una sola mirada?**
Sí, los títulos están bien diferenciados del contenido, las tarjetas usan íconos de colores y los botones de acción principal se destacan visualmente.

**¿Los formularios ofrecen opciones que permitan al usuario evitar, cancelar o rehacer una acción?**
Sí, los formularios incluyen botones de "Cancelar" o "Volver" además del botón de guardar. También se implementa un modal de confirmación reutilizable para acciones destructivas como eliminaciones.

**¿El tamaño de la letra de los textos es adecuado y ajustable o modificable por el usuario usando las herramientas del programa visualizador?**
Sí, se utiliza Bootstrap 5 con diseño responsivo que se adapta a cada tamaño de pantalla. Los tamaños de fuente son relativos y pueden ser ajustados por el navegador del usuario.

**¿Los vínculos, imágenes e íconos son claramente visibles y distinguibles?**
Sí, se distinguen claramente todos los elementos de navegación. Los botones usan colores consistentes y los íconos de Font Awesome son representativos de cada acción.

**¿Los vínculos (links) visitados y no visitados son claramente diferenciables?**
No, no se implementó una diferenciación visual explícita entre enlaces visitados y no visitados.

**¿Los íconos son representativos de la función o acción que realizan y son aclarados mediante una etiqueta ALT en HTML?**
Sí, los íconos de Font Awesome son representativos de cada función (huella para mascotas, estetoscopio para atenciones, engranajes para servicios, periódico para novedades, etc.). Al ser íconos implementados con `<i>`, no requieren `alt` pero se complementan con texto descriptivo.

**¿Todas las páginas cuentan con un título que indique el nombre de la institución e información de contactos virtuales y físicos al pie de la página?**
Sí, todas las páginas cuentan con el nombre "Veterinaria San Antón" en el navbar y el footer incluye el copyright y enlaces a la página de Contacto con información completa.

**¿Provee información del organigrama de la institución? ¿Incluye nombres actualizados de las autoridades y la forma de contactarlos?**
No, ya que el sitio está orientado a la gestión de mascotas, servicios y atenciones veterinarias. No es necesario un organigrama institucional.

**¿El nombre de la URL está vinculado con el nombre o función de la institución y se ofrece en la barra superior del programa visualizador?**
Sí, el título de la página muestra "Veterinaria San Antón" en la barra del navegador.

**¿Ofrece el Sitio contenidos sobre la visión, misión, objetivos y plan estratégico de la institución?**
Sí, la página "¿Quiénes somos?" incluye secciones de Misión, Visión y Valores de la veterinaria.

**En el caso que existan palabras técnicas en los contenidos del Sitio, ¿existe una sección de glosario que las explique? ¿Es fácil llegar a él?**
No, el sitio no cuenta con un glosario. Sin embargo, el lenguaje utilizado es accesible y no requiere terminología técnica compleja.

**¿Ofrece páginas de ayuda que explican cómo usar el Sitio Web?**
No, actualmente no se ofrece una sección de ayuda. La interfaz es intuitiva y los formularios incluyen indicaciones claras para guiar al usuario.

**¿Ofrece área de Preguntas Frecuentes con datos de ayuda a usuarios?**
No, el sitio no cuenta con una sección de Preguntas Frecuentes.

**En caso de errores de consistencia dentro del sitio, ¿se ofrece un mensaje personalizado mediante una página explicativa? (Por ejemplo: Error 404 para página inexistente)**
Sí, el sitio cuenta con una página personalizada de Error 404 con un diseño temático (ícono de huella animado), mensaje amigable y botones de navegación para volver al inicio o regresar.

---

## TEST DE VALIDACIÓN DE ESTÁNDARES

- **Markup Validation:** Al realizar la validación con el validador de W3C, el sitio pasa la validación satisfactoriamente sin errores ni advertencias en el HTML propio del proyecto.

- **CSS Validation:** Los errores y advertencias encontrados corresponden exclusivamente a los archivos internos de Bootstrap. El CSS personalizado del sitio (`style.css`) pasa todas las validaciones sin inconvenientes.

- **TAW – Accesibilidad:** Se realizaron pruebas de accesibilidad y el código propio del sitio cumple con las condiciones de accesibilidad controlada. Los únicos señalamientos corresponden a código particular de Bootstrap, principalmente etiquetas `<i>` utilizadas para íconos dentro de enlaces `<a>`, que es una práctica estándar del framework Font Awesome.
