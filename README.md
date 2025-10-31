# MTZ Slider (v2.3.0)

Plugin moderno y responsive para WordPress que permite crear sliders de imágenes con un panel de administración intuitivo.

## 🚀 Características

- ✅ **8 Efectos de Animación**: Fade, Slide (Horizontal/Vertical), Zoom (In/Out), Flip (Horizontal/Vertical), Cubo 3D
- ✅ **Selector de Efectos**: Cambia el efecto de animación desde el panel de administración
- ✅ **Slider Horizontal Responsive**: Las imágenes se adaptan al 100% del ancho del viewport
- ✅ **Panel Administrativo**: Gestión completa de imágenes desde WordPress
- ✅ **API REST**: Integración moderna con WordPress REST API
- ✅ **Base de Datos Segura**: Tablas personalizadas con prefijo `mtz-slider`
- ✅ **Arrastra y Suelta**: Reordena las imágenes fácilmente
- ✅ **Autoplay**: Reproducción automática configurable
- ✅ **Botón por imagen en frontend**: "Conocer más..." con URL configurable
- ✅ **Íconos Lucide**: Unificación a la librería Lucide en admin y frontend
- ✅ **Navegación por Teclado**: Soporte para flechas del teclado
- ✅ **Swipe Mejorado**: Navegación por gestos en dispositivos móviles y tablets con prevención de scroll accidental
- ✅ **Contenido Siempre Centrado**: Textos y botones permanecen centrados en todas las animaciones
- ✅ **Responsive**: Diseño adaptable a cualquier dispositivo
- ✅ **UI/UX Moderna**: Interfaz intuitiva y atractiva
- ✅ **Actualizaciones Automáticas**: Sistema de actualización automática desde GitHub Releases

## 📋 Requisitos

- WordPress 5.8 o superior
- PHP 7.4 o superior

## 🔧 Instalación

1. Descarga o clona el repositorio en la carpeta de plugins de WordPress:
```bash
wp-content/plugins/mtz-slider/
```

2. Activa el plugin desde el panel de administración de WordPress

3. Accede a **MTZ Slider** en el menú lateral para comenzar a agregar imágenes

## 🔄 Actualizaciones Automáticas

El plugin incluye soporte para actualizaciones automáticas desde GitHub Releases. Cuando haya una nueva versión disponible:

1. WordPress te mostrará un aviso en **Plugins → Plugins instalados**
2. Haz clic en **"Actualizar ahora"** para instalar la nueva versión
3. El plugin se actualizará automáticamente sin necesidad de descargar manualmente

**Nota para desarrolladores**: Para crear releases y habilitar actualizaciones automáticas, consulta el archivo `CREAR-RELEASE.md` en el repositorio.

## 📖 Uso

### Añadir Imágenes al Slider

1. Ve a **MTZ Slider** en el menú administrativo
2. Haz clic en **Agregar Imágenes**
3. Selecciona las imágenes desde la biblioteca de medios
4. Edita los detalles de cada imagen haciendo clic en el icono de editar
5. Arrastra las imágenes para reordenarlas

### Usar el Slider

**Shortcode:**
```
[mtz_slider]
```

**Con opciones:**
```
[mtz_slider autoplay="true" speed="5000"]
```

**En archivos PHP:**
```php
<?php echo do_shortcode('[mtz_slider]'); ?>
```

### Parámetros del Shortcode

- `autoplay`: Activa/desactiva la reproducción automática (true/false)
- `speed`: Velocidad del autoplay en milisegundos (por defecto: 5000)

## 🏗️ Estructura del Plugin

```
mtz-slider/
├── mtz-slider.php          # Archivo principal
├── includes/               # Clases principales
│   ├── class-mtz-slider-database.php
│   ├── class-mtz-slider-api.php
│   ├── class-mtz-slider-admin.php
│   └── class-mtz-slider-public.php
├── admin/                  # Panel administrativo
│   └── views/
│       └── admin-page.php
├── public/                 # Frontend
│   └── views/
│       └── slider.php
├── assets/                 # Recursos
│   ├── css/
│   │   ├── admin.css
│   │   └── public.css
│   └── js/
│       ├── admin.js
│       └── public.js
├── composer.json
├── package.json
├── .gitignore
└── README.md
```

## 🗄️ Base de Datos

El plugin crea automáticamente las siguientes tablas/campos:

- `wp_mtz_slider_images`: Almacena las imágenes del slider
  - `id`: ID único de la imagen
  - `image_id`: ID de la imagen en WordPress
  - `image_url`: URL de la imagen
  - `link_url`: URL asociada al botón “Conocer más...”
  - `image_title`: Título de la imagen
  - `image_description`: Descripción
  - `image_alt`: Texto alternativo
  - `sort_order`: Orden de visualización
  - `is_active`: Estado activo/inactivo
  - `created_at`: Fecha de creación
  - `updated_at`: Fecha de actualización

## 🎨 Personalización

### Estilos CSS

Los estilos públicos se encuentran en `assets/css/public.css`. Puedes sobrescribirlos desde tu tema hijo. Altura por defecto: `80vh` (antes `60vh`). Ejemplo:

```css
.mtz-slide { height: 80vh; }
```

### JavaScript

El JavaScript del slider está en `assets/js/public.js`. Puedes extender su funcionalidad desde tu tema.

## 🔒 Seguridad

- ✅ Verificación de noces en todas las peticiones AJAX
- ✅ Sanitización de datos de entrada
- ✅ Preparación de consultas SQL
- ✅ Verificación de capacidades de usuario
- ✅ Escape de datos de salida

## 📝 Licencia

GPL v2 o posterior

## 👤 Autor

Fabio Jara

## 🙏 Contribuciones

Las contribuciones son bienvenidas. Por favor:

1. Fork el repositorio
2. Crea una rama para tu característica (`git checkout -b feature/nueva-caracteristica`)
3. Commit tus cambios (`git commit -am 'Agrega nueva característica'`)
4. Push a la rama (`git push origin feature/nueva-caracteristica`)
5. Abre un Pull Request

## 📧 Soporte

Para reportar problemas o sugerencias, abre un issue en GitHub.

## 🗓️ Changelog

Consulta `ACTUALIZACION.md` para ver el historial de cambios por versión.

