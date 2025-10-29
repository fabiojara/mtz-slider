# 📦 Guía de Instalación - MTZ Slider

## Requisitos Previos

- WordPress 5.8 o superior
- PHP 7.4 o superior
- Servidor web (Apache, Nginx, etc.)

## Instalación

### Opción 1: Instalación Manual

1. **Descarga el plugin**
   - Descarga el archivo ZIP o clona el repositorio:
   ```bash
   git clone https://github.com/tu-usuario/mtz-slider.git
   ```

2. **Sube el plugin a WordPress**
   - Extrae el contenido en la carpeta de plugins:
   ```
   wp-content/plugins/mtz-slider/
   ```

3. **Activa el plugin**
   - Ve a tu panel de WordPress → Plugins
   - Busca "MTZ Slider"
   - Haz clic en "Activar"

### Opción 2: Instalación vía WordPress Admin

1. Ve a **Plugins → Añadir nuevo**
2. Haz clic en **Subir plugin**
3. Selecciona el archivo ZIP del plugin
4. Haz clic en **Instalar ahora**
5. Activa el plugin

## Configuración Inicial

Una vez activado:

1. Ve a **MTZ Slider** en el menú lateral de WordPress
2. Haz clic en **Agregar Imágenes**
3. Selecciona las imágenes que deseas mostrar en el slider
4. Edita los detalles de cada imagen (título, descripción, texto alternativo)
5. Reordena las imágenes arrastrándolas

## Usar el Slider

### Shortcode Básico

```
[mtz_slider]
```

### Shortcode con Opciones

```
[mtz_slider autoplay="true" speed="5000"]
```

### En un Archivo PHP

```php
<?php echo do_shortcode('[mtz_slider]'); ?>
```

### En el Editor de Bloques

Busca el bloque "MTZ Slider" o usa el shortcode en un bloque HTML.

## Parámetros Disponibles

| Parámetro | Descripción | Valores | Por defecto |
|-----------|-------------|---------|-------------|
| `autoplay` | Activa reproducción automática | true/false | true |
| `speed` | Velocidad en milisegundos | número | 5000 |

## Desinstalación

1. Ve a **Plugins → Plugins instalados**
2. Busca "MTZ Slider"
3. Haz clic en **Desactivar**
4. Haz clic en **Eliminar**

**Nota:** La tabla de base de datos `wp_mtz_slider_images` permanecerá en la base de datos. Si deseas eliminarla completamente, ejecuta esta consulta SQL:

```sql
DROP TABLE IF EXISTS wp_mtz_slider_images;
```

## Solución de Problemas

### El slider no se muestra

- Verifica que hay imágenes agregadas en el panel de administración
- Revisa que el shortcode esté correctamente escrito
- Limpia la caché de tu sitio

### Las imágenes no se cargan

- Verifica que las imágenes estén subidas correctamente a la biblioteca de medios
- Revisa los permisos de las carpetas de WordPress
- Comprueba la ruta de las imágenes

### El panel de administración no carga

- Verifica que tienes permisos de administrador
- Revisa la consola del navegador para errores
- Asegúrate de que WordPress esté actualizado

## Soporte

Si encuentras algún problema:

1. Revisa esta documentación
2. Busca en los [Issues de GitHub](https://github.com/tu-usuario/mtz-slider/issues)
3. Crea un nuevo issue con detalles del problema

