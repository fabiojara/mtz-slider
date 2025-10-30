# 🔄 Instrucciones de Actualización - Versión 2.2.0

## ⚠️ IMPORTANTE: Actualización a Versión 2.2.0

Si estás actualizando desde una versión anterior, sigue estos pasos:

### Paso 1: Desactivar el Plugin
1. Ve a **Plugins → Plugins Instalados**
2. Busca "MTZ Slider"
3. Haz clic en **Desactivar**

### Paso 2: Reactivar el Plugin
1. Haz clic en **Activar** para el plugin MTZ Slider
2. Esto actualizará automáticamente las tablas de la base de datos

### Paso 3: Verificar que Funcione
1. Ve a **MTZ Slider** en el menú lateral
2. Haz clic en **Crear Nuevo Slider**
3. Ingresa un nombre (ej: "Slider Principal")
4. Haz clic en **Guardar**
5. Debería aparecer en la lista de sliders

## 🐛 Solución de Problemas

### Si la modal no se cierra al guardar:

1. **Abre la consola del navegador** (F12)
2. **Actualiza la página** (F5)
3. **Intenta crear el slider de nuevo**
4. Verifica en la consola si hay errores

### Si aparece error 500:

1. Ve a **Plugins**
2. **Desactiva** el plugin
3. **Elimina** el plugin
4. Vuelve a **instalar** el plugin
5. Esto recreará las tablas correctamente

### Ver los logs de error:

Si tienes acceso a los logs de WordPress, verifica:
- `wp-content/debug.log`
- Errores en `error_log` de PHP

## ✅ Cambios en la Versión 2.2.0

- ✅ Altura por defecto del slider cambiada a `80vh` (antes `60vh`)
- ✅ Lazy-loading y `srcset/sizes` en imágenes del slider
- ✅ Autoplay pausado cuando el slider no está en viewport (IntersectionObserver)
- ✅ Frontend reescrito sin jQuery (Vanilla JS)
- ✅ Carga condicional de assets solo cuando existe el shortcode en la página
- ✅ Preparación de build con Vite (minificación y cache busting)

## 📋 Shortcode Nuevo Formato

**Antes (Versión 1.0):**
```
[mtz_slider]
```

**Ahora (Versión 2.0):**
```
[mtz_slider id="1"]
```

Cada slider tiene un ID único que puedes ver en el panel de administración.

## 🆘 ¿Necesitas Ayuda?

Si sigues teniendo problemas después de seguir estos pasos, verifica:

1. Que WordPress esté actualizado a la versión 5.8 o superior
2. Que PHP sea 7.4 o superior
3. Que no haya errores de sintaxis en los archivos del plugin
4. Los permisos de la base de datos estén correctos

