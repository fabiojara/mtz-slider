# 📁 Estructura Correcta del Plugin

## ⚠️ Problema Detectado

El error que recibiste indica que el plugin no encuentra sus archivos. Esto suele ocurrir cuando:

1. **El ZIP tiene estructura incorrecta** (carpeta dentro de carpeta)
2. **Los archivos no se extrajeron correctamente**
3. **El plugin está en una carpeta con nombre diferente**

## ✅ Estructura Correcta del Plugin

Después de descomprimir el ZIP, la estructura debe ser:

```
wp-content/plugins/mtz-slider-1/
├── mtz-slider.php          ← Archivo principal (debe estar aquí)
├── includes/
│   ├── class-mtz-slider-database.php
│   ├── class-mtz-slider-api.php
│   ├── class-mtz-slider-admin.php
│   ├── class-mtz-slider-public.php
│   ├── class-mtz-slider-updater.php
│   └── index.php
├── admin/
│   └── views/
│       └── admin-page.php
├── public/
│   └── views/
│       └── slider.php
└── assets/
    ├── css/
    │   ├── admin.css
    │   └── public.css
    └── js/
        ├── admin.js
        └── public.js
```

## 🔍 Cómo Verificar la Estructura

1. **Accede por FTP o cPanel File Manager** a:
   ```
   wp-content/plugins/mtz-slider-1/
   ```

2. **Verifica que veas directamente**:
   - El archivo `mtz-slider.php` (sin subcarpetas intermedias)
   - La carpeta `includes/` en el mismo nivel que `mtz-slider.php`

3. **Si ves esto (INCORRECTO)**:
   ```
   mtz-slider-1/
   └── mtz-slider/          ← Carpeta extra (PROBLEMA)
       ├── mtz-slider.php
       └── includes/
   ```

4. **Solución**: Mueve el contenido de `mtz-slider/` hacia arriba

## 🔧 Solución Paso a Paso

### Opción 1: Reinstalar Correctamente

1. **Elimina el plugin actual**:
   - Ve a WordPress Admin → Plugins
   - Desactiva y elimina "MTZ Slider"

2. **Descarga el ZIP correcto** desde:
   ```
   https://github.com/fabiojara/mtz-slider/releases/download/v2.2.1/mtz-slider.zip
   ```

3. **Antes de subir el ZIP**, descomprímelo localmente y verifica la estructura:
   - Debe tener `mtz-slider.php` en la raíz del ZIP
   - Debe tener la carpeta `includes/` en la raíz del ZIP

4. **Si el ZIP tiene doble carpeta**:
   - Abre el ZIP
   - Si ves `mtz-slider/mtz-slider.php`, extrae el contenido de `mtz-slider/` a una nueva carpeta
   - Comprime esa nueva carpeta y usa ese ZIP

5. **Sube e instala el ZIP corregido**

### Opción 2: Corregir Instalación Actual

Si prefieres corregir la instalación actual:

1. **Accede por FTP/cPanel** a:
   ```
   wp-content/plugins/mtz-slider-1/
   ```

2. **Si hay una subcarpeta `mtz-slider/`**:
   - Mueve todos los archivos de `mtz-slider/` a `mtz-slider-1/`
   - Elimina la carpeta vacía `mtz-slider/`

3. **Verifica que ahora `mtz-slider.php` esté directamente en**:
   ```
   wp-content/plugins/mtz-slider-1/mtz-slider.php
   ```

4. **Intenta activar el plugin nuevamente**

## 📋 Verificación Rápida

Abre en tu navegador o por FTP y verifica:

```
✅ CORRECTO:
wp-content/plugins/mtz-slider-1/mtz-slider.php
wp-content/plugins/mtz-slider-1/includes/class-mtz-slider-database.php

❌ INCORRECTO:
wp-content/plugins/mtz-slider-1/mtz-slider/mtz-slider.php
wp-content/plugins/mtz-slider-1/mtz-slider/includes/class-mtz-slider-database.php
```

## 💡 Nota Importante

El código del plugin ya se actualizó para manejar mejor estos errores, pero lo más importante es asegurarse de que el ZIP tenga la estructura correcta desde el principio.

Si después de verificar la estructura el problema persiste, el código mejorado te dará un mensaje de error más claro indicando exactamente qué archivo falta y dónde lo está buscando.

