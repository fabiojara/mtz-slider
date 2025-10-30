# 🔄 Instrucciones de actualización y changelog

Este documento cubre instrucciones de actualización y el historial de cambios por versión.

## ⚠️ Pasos recomendados al actualizar

1. Ve a **Plugins → Plugins instalados**.
2. Desactiva **MTZ Slider**.
3. Activa nuevamente **MTZ Slider** para aplicar migraciones de BD si corresponde.
4. Limpia caché del navegador (Ctrl/Cmd + F5) y/o del sitio si usas plugins de caché.

Si tras activar no ves cambios, revisa consola del navegador y logs de WordPress.

### Dónde ver logs
- `wp-content/debug.log` (si `WP_DEBUG_LOG` está habilitado)
- `error_log` de PHP del servidor

---

## 🗓️ Changelog

### 2.2.1
- Documentación actualizada (README, guía de actualización).
- Sincronización de metadatos: autor y repositorio en `package.json`.
- No hay cambios funcionales en código.

### 2.2.0
- Altura por defecto del slider: `80vh` (antes `60vh`).
- Lazy-loading y `srcset/sizes` en imágenes del slider.
- Autoplay pausado cuando el slider no está en viewport (IntersectionObserver).
- Frontend reescrito en Vanilla JS (eliminado jQuery).
- Carga condicional de assets solo cuando existe el shortcode en la página.
- Preparado build con Vite (minificación y cache busting).

### 2.1.0
- Botón “Conocer más…” por imagen con URL configurable.
- Migración automática de BD: nuevo campo `link_url`.
- Unificación de íconos a Lucide en admin y frontend.
- Dots circulares y corrección de estados `focus/active` en flechas.
- Ajustes de textos de ayuda en admin.

### 2.0.0
- Soporte para múltiples sliders con shortcode por ID: `[mtz_slider id="1"]`.
- Nueva interfaz con lista lateral y reordenamiento drag & drop.
- Mejoras de manejo de errores y logs.

---

## 📋 Formato del shortcode

Ejemplo básico:
```
[mtz_slider]
```

Con ID específico y opciones:
```
[mtz_slider id="1" autoplay="true" speed="5000"]
```

Cada slider tiene un ID único visible en el panel de administración.

---

## 🆘 ¿Necesitas ayuda?

1. WordPress 5.8 o superior.
2. PHP 7.4 o superior.
3. Sin errores de sintaxis en archivos del plugin.
4. Permisos correctos en la base de datos.

