# Lucide Icons en MTZ Slider

## 🎨 Librería de Iconos: Lucide

El plugin MTZ Slider ahora utiliza **Lucide Icons**, una librería moderna de iconos SVG.

**Sitio oficial:** https://lucide.dev

### ✨ Características

- **1640+ iconos disponibles**
- **Ligero y escalable** - SVG optimizado
- **Consistente** - Diseño limpio y uniforme
- **Customizable** - Color, tamaño, ancho de línea
- **Active community** - Mantenimiento activo
- **CDN gratuito** - https://unpkg.com/lucide

### 🎯 Iconos Utilizados

#### Frontend (Slider Público)

| Icono | Nombre Lucide | Uso |
|-------|--------------|-----|
| ⬅️ | `chevron-left` | Botón anterior |
| ➡️ | `chevron-right` | Botón siguiente |
| ⏸️ | `pause` | Pausar/Reproducir |

#### Panel Administrativo

| Icono | Nombre Lucide | Uso |
|-------|--------------|-----|
| ➕ | `plus` | Crear nuevo slider |
| 🖼️ | `image-plus` | Agregar imágenes |
| 🗑️ | `trash-2` | Eliminar |
| 📋 | `copy` | Copiar shortcode |

### 📦 Instalación

Lucide se carga automáticamente desde CDN:

```php
wp_enqueue_script('lucide', 'https://unpkg.com/lucide@latest/dist/umd/lucide.js');
```

### 🚀 Inicialización

```javascript
// Frontend
if (typeof lucide !== 'undefined') {
  lucide.createIcons();
}

// Admin
if (typeof lucide !== 'undefined') {
  lucide.createIcons();
}
```

### 💻 Uso en el Código

#### En PHP/HTML:

```html
<i data-lucide="chevron-left"></i>
<i data-lucide="chevron-right"></i>
<i data-lucide="pause"></i>
```

#### En JavaScript:

```javascript
lucide.createIcons(); // Inicializa todos los iconos con data-lucide
```

### 🎨 Personalización de Estilos

Los iconos Lucide usan SVG con atributo `stroke`. Puedes personalizarlos con CSS:

```css
/* Tamaño del icono */
i[data-lucide] {
    width: 24px;
    height: 24px;
}

/* Color del trazo */
i[data-lucide] {
    stroke: #333;
}

/* Ancho del trazo */
i[data-lucide] {
    stroke-width: 2;
}

/* Color al hover */
i[data-lucide]:hover {
    stroke: #2271b1;
}
```

### 📚 Recursos

- **Sitio web:** https://lucide.dev
- **Documentación:** https://lucide.dev/icons/
- **GitHub:** https://github.com/lucide-icons/lucide
- **CDN:** https://unpkg.com/lucide@latest/dist/umd/lucide.js

### 🔍 Buscar Iconos

Visita https://lucide.dev/icons/ para buscar entre 1640+ iconos disponibles.

### 📝 Ejemplos de Otros Iconos Populares

```html
<!-- Navegación -->
<i data-lucide="arrow-left"></i>
<i data-lucide="arrow-right"></i>
<i data-lucide="arrow-up"></i>
<i data-lucide="arrow-down"></i>

<!-- Acciones -->
<i data-lucide="check"></i>
<i data-lucide="x"></i>
<i data-lucide="trash"></i>
<i data-lucide="edit"></i>

<!-- Multimedia -->
<i data-lucide="play"></i>
<i data-lucide="pause"></i>
<i data-lucide="image"></i>
<i data-lucide="video"></i>
```

### ⚠️ Notas

- Los iconos se cargan desde CDN (https://unpkg.com)
- Funciona offline después de la primera carga
- Compatible con todos los navegadores modernos
- SVG se renderiza perfectamente en cualquier tamaño

