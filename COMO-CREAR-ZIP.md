# 📦 Cómo Crear el ZIP Correcto del Plugin

## ✅ Estructura Correcta del ZIP

El ZIP debe tener esta estructura exacta:

```
mtz-slider.zip
└── mtz-slider/              ← Carpeta principal (IMPORTANTE)
    ├── mtz-slider.php       ← Archivo principal
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
    ├── assets/
    │   ├── css/
    │   │   ├── admin.css
    │   │   └── public.css
    │   └── js/
    │       ├── admin.js
    │       └── public.js
    └── ... (otros archivos)
```

**IMPORTANTE**: Dentro del ZIP debe haber una carpeta llamada `mtz-slider/` que contenga todo el contenido.

## 🔧 Método Manual con Windows

### Paso 1: Copiar la carpeta del plugin

1. Ve a: `c:\laragon\www\variospluginswp\wp-content\plugins\`
2. **Copia** la carpeta `mtz-slider` (no mover, solo copiar)
3. Pégala en el Escritorio o en una carpeta temporal

### Paso 2: Limpiar archivos innecesarios

Dentro de la carpeta copiada, **elimina**:
- `node_modules/` (carpeta completa)
- `.git/` (carpeta completa, si existe)
- `.gitignore`
- `vite.config.js`
- `package.json`
- `package-lock.json`
- `composer.json`
- `composer.lock`
- Todos los archivos `.ps1`
- `debug-activation.php`
- Cualquier archivo `.zip` o `.log`

### Paso 3: Crear el ZIP

1. **Selecciona la carpeta** `mtz-slider` (no el contenido, la carpeta completa)
2. **Clic derecho** → **Enviar a** → **Carpeta comprimida (en ZIP)**
3. Windows creará `mtz-slider.zip`
4. **Renombra** si es necesario para asegurarte de que se llama exactamente `mtz-slider.zip`

### Paso 4: Verificar la Estructura

**Antes de subir**, verifica que el ZIP tenga la estructura correcta:

1. **Abre el ZIP** (doble clic en `mtz-slider.zip`)
2. **Debes ver**:
   ```
   mtz-slider/          ← Carpeta principal
   ├── mtz-slider.php
   ├── includes/
   └── ...
   ```

3. **NO debe verse**:
   ```
   mtz-slider/          ← Carpeta extra (INCORRECTO)
   └── mtz-slider/      ← Otra carpeta dentro
       ├── mtz-slider.php
       └── ...
   ```

## ✅ Resultado Esperado

Cuando instalas el ZIP en WordPress:

1. WordPress lo descomprime en: `wp-content/plugins/mtz-slider/`
2. El plugin se activa correctamente
3. **NO** se crea `mtz-slider-1/` (a menos que ya exista otra carpeta `mtz-slider`)

## ⚠️ Si WordPress crea `mtz-slider-1/`

Esto sucede si ya existe una carpeta `mtz-slider` en el servidor. Solución:

1. **Elimina** la carpeta `mtz-slider-1/` y su contenido desde WordPress Admin → Plugins
2. **Elimina** también la carpeta `mtz-slider/` si existe y tiene una instalación corrupta
3. **Instala** el ZIP nuevamente
4. Ahora debería crear `mtz-slider/` sin el sufijo `-1`

## 📝 Checklist Antes de Subir

- [ ] El ZIP se llama `mtz-slider.zip`
- [ ] Dentro del ZIP hay una carpeta `mtz-slider/`
- [ ] `mtz-slider.php` está dentro de `mtz-slider/`
- [ ] `includes/class-mtz-slider-database.php` existe dentro de `mtz-slider/includes/`
- [ ] No hay `node_modules/` ni `.git/` en el ZIP
- [ ] El tamaño del ZIP es razonable (más de 50 KB)

## 🚀 Instalación

1. Ve a **WordPress Admin** → **Plugins** → **Añadir nuevo**
2. Clic en **Subir plugin**
3. Selecciona `mtz-slider.zip`
4. Clic en **Instalar ahora**
5. Clic en **Activar plugin**

Después de activar, WordPress debería crear la carpeta:
```
wp-content/plugins/mtz-slider/
```

Y el plugin debería funcionar correctamente.

