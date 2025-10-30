# 🔧 Solución de Error Fatal al Activar el Plugin

Si recibes un error fatal al intentar activar el plugin, sigue estos pasos para diagnosticar y solucionar el problema.

## 📋 Paso 1: Obtener el Mensaje de Error Completo

### En el panel de WordPress:

1. Si ves un mensaje genérico como "El plugin no ha podido activarse porque ha provocado un error fatal", necesitas ver el error completo.

2. **Habilitar Debug en WordPress**:
   - Edita el archivo `wp-config.php` en la raíz de tu WordPress
   - Agrega o modifica estas líneas (antes de "That's all, stop editing!"):
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   define('WP_DEBUG_DISPLAY', false);
   ```

3. **Ver el error**:
   - Intenta activar el plugin nuevamente
   - El error completo aparecerá en: `wp-content/debug.log`
   - O si `WP_DEBUG_DISPLAY` está en `true`, aparecerá en pantalla

### O desde el panel de hosting/cPanel:

1. Busca los logs de error de PHP
2. El error debería aparecer cuando intentas activar el plugin

## 🔍 Paso 2: Errores Comunes y Soluciones

### Error: "Call to undefined function"

**Causa**: Funciones de WordPress no disponibles durante la activación.

**Solución**: Ya se corrigió en la versión actual. Si persiste, verifica la versión de WordPress (mínimo 5.8).

### Error: "Class 'MTZ_Slider_Database' not found"

**Causa**: Archivo de clase no encontrado o ruta incorrecta.

**Solución**:
1. Verifica que todos los archivos estén en su lugar
2. Asegúrate de que el ZIP tenga la estructura correcta:
   ```
   mtz-slider/
   ├── mtz-slider.php
   ├── includes/
   │   ├── class-mtz-slider-database.php
   │   └── ...
   ```

### Error: "Cannot redeclare class"

**Causa**: El plugin se está cargando dos veces.

**Solución**:
1. Desactiva todas las instancias del plugin
2. Elimina el plugin completamente
3. Reinstálalo desde cero

### Error: "Parse error" o "syntax error"

**Causa**: Error de sintaxis en algún archivo PHP.

**Solución**:
1. Verifica la versión de PHP (mínimo 7.4)
2. Revisa `wp-content/debug.log` para ver en qué archivo está el error
3. Verifica que el ZIP no esté corrupto

### Error de permisos de base de datos

**Causa**: WordPress no puede crear tablas.

**Solución**:
1. Verifica que el usuario de la base de datos tenga permisos CREATE
2. Verifica la conexión a la base de datos en `wp-config.php`

## ✅ Paso 3: Verificación Rápida

1. **Verifica la versión de PHP**:
   - Mínimo: PHP 7.4
   - Recomendado: PHP 8.0 o superior

2. **Verifica la versión de WordPress**:
   - Mínimo: WordPress 5.8
   - Actual: WordPress 6.x

3. **Verifica la estructura del ZIP**:
   - Debe tener `mtz-slider.php` en la raíz del ZIP
   - Debe tener la carpeta `includes/` con todas las clases

## 🔄 Paso 4: Reinstalación Limpia

Si nada funciona, haz una reinstalación limpia:

1. **Desactiva y elimina el plugin** desde WordPress
2. **Limpia la base de datos** (opcional, solo si quieres empezar de cero):
   ```sql
   DROP TABLE IF EXISTS wp_mtz_slider_sliders;
   DROP TABLE IF EXISTS wp_mtz_slider_images;
   ```
   (Reemplaza `wp_` por el prefijo de tu base de datos)

3. **Descarga el ZIP más reciente** desde:
   https://github.com/fabiojara/mtz-slider/releases

4. **Instala el plugin nuevamente**

## 📞 Paso 5: Si el Problema Persiste

Si después de seguir todos los pasos el problema persiste, necesito:

1. **El mensaje de error completo** de `wp-content/debug.log`
2. **Versión de WordPress** (Administración → Información del sistema)
3. **Versión de PHP** (Administración → Información del sistema o phpinfo)
4. **Estructura del ZIP** que usaste (qué archivos contiene)

Con esta información puedo ayudarte a solucionar el problema específico.

## 📝 Notas Técnicas

- El plugin crea dos tablas en la base de datos durante la activación:
  - `{prefix}_mtz_slider_sliders`
  - `{prefix}_mtz_slider_images`

- Si la activación falla, estas tablas pueden quedar parcialmente creadas
- En ese caso, elimina las tablas manualmente y vuelve a intentar

## 🚀 ZIP Correcto para Instalación

Asegúrate de usar el ZIP del release oficial desde GitHub, que tiene:
- ✅ Todos los archivos PHP necesarios
- ✅ Carpetas `includes/`, `admin/`, `public/`, `assets/`
- ✅ Sin archivos de desarrollo (`.git`, `node_modules`)
- ✅ Estructura correcta para WordPress

