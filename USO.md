# 🎯 Cómo Insertar el Slider MTZ

## Método 1: Usando el Shortcode en el Editor

### En el Editor Gutenberg (Bloques)

1. **Desde el Editor de Páginas/Entradas:**
   - Haz clic en el botón **"+"** para agregar un bloque
   - Busca **"Shortcode"** o **"Código corto"**
   - Escribe: `[mtz_slider]`
   - Publica o actualiza la página

### En el Editor Clásico

1. En el editor de páginas o entradas, simplemente escribe:
```
[mtz_slider]
```

2. Publica o actualiza la página

## Método 2: Insertar con Opciones

### Con Autoplay Configurado

```
[mtz_slider autoplay="true" speed="5000"]
```

- `autoplay="true"` - Activa la reproducción automática
- `autoplay="false"` - Desactiva la reproducción automática
- `speed="5000"` - Velocidad en milisegundos (5000 = 5 segundos)

### Ejemplo con Opciones Personalizadas

```
[mtz_slider autoplay="false" speed="3000"]
```

## Método 3: Insertar en Código PHP (Template del Tema)

### En un archivo PHP del tema (como `page.php`, `single.php`, etc.)

```php
<?php echo do_shortcode('[mtz_slider]'); ?>
```

### O con opciones:

```php
<?php echo do_shortcode('[mtz_slider autoplay="true" speed="4000"]'); ?>
```

## Método 4: Insertar en Widget o Sidebar

Si tu tema soporta shortcodes en widgets:

1. Ve a **Apariencia → Widgets**
2. Agrega un widget **Texto** o **HTML**
3. Escribe: `[mtz_slider]`
4. Guarda el widget

## Método 5: Insertar en Menú de Navegación

Para insertarlo en el archivo header.php de tu tema:

```php
<!-- Colocar después de <body> -->
<div class="slider-container">
    <?php echo do_shortcode('[mtz_slider]'); ?>
</div>
```

## 🎨 Personalización del Slider

### Cambiar la Velocidad

Para que pase más rápido o más lento:

```
[mtz_slider speed="3000"]  <!-- 3 segundos -->
[mtz_slider speed="7000"]  <!-- 7 segundos -->
```

### Desactivar Autoplay

Para que el usuario tenga control manual:

```
[mtz_slider autoplay="false"]
```

## 📍 Ubicaciones Típicas

### En la Página de Inicio

Edita tu página de inicio (homepage) y agrega el shortcode al principio.

### En el Header

```php
<!-- En header.php del tema -->
<?php if (is_front_page()) { 
    echo do_shortcode('[mtz_slider]'); 
} ?>
```

### En el Footer

```php
<!-- En footer.php del tema -->
<?php echo do_shortcode('[mtz_slider]'); ?>
```

### En un Template Personalizado

```php
<!-- En page-slider.php del tema -->
<?php get_header(); ?>

<div class="mtz-slider-container">
    <?php echo do_shortcode('[mtz_slider]'); ?>
</div>

<?php get_footer(); ?>
```

## 🎛️ Ejemplos Completos

### Ejemplo 1: Slider Rápido

```
[mtz_slider autoplay="true" speed="2000"]
```

### Ejemplo 2: Slider Manual (Sin Autoplay)

```
[mtz_slider autoplay="false"]
```

### Ejemplo 3: Slider Lento

```
[mtz_slider autoplay="true" speed="8000"]
```

## ⚙️ Parámetros Disponibles

| Parámetro | Valores | Por Defecto | Descripción |
|-----------|---------|-------------|-------------|
| `autoplay` | true/false | true | Activa/desactiva reproducción automática |
| `speed` | número en ms | 5000 | Velocidad entre transiciones |

## 🚀 Consejos de Rendimiento

1. **Optimiza tus imágenes** antes de subirlas (usa herramientas como TinyPNG)
2. **No uses demasiadas imágenes** (3-5 imágenes es ideal)
3. **Mantén las imágenes con tamaños similares** para mejor visualización

## ❓ Solución de Problemas

### El slider no aparece

1. Verifica que hayas agregado imágenes desde el panel de administración
2. Asegúrate de que el shortcode esté escrito correctamente
3. Revisa que no haya errores en la consola del navegador (F12)

### Solo se muestra una imagen

1. Agrega más imágenes desde **MTZ Slider** en el menú de WordPress
2. Asegúrate de que las imágenes estén activas (is_active = 1)

## 📞 Soporte

Para más ayuda, consulta el archivo README.md o abre un issue en GitHub.

