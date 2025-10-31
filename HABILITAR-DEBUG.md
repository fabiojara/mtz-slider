# 🔍 Cómo Ver el Error Fatal Exacto

## 📋 Opción 1: Habilitar Debug en WordPress (Recomendado)

### Pasos:

1. **Accede a tu servidor** (por FTP, cPanel File Manager, o SSH)

2. **Edita el archivo `wp-config.php`** que está en la raíz de WordPress (donde están wp-load.php, wp-settings.php, etc.)

3. **Busca esta línea** (cerca del final):
   ```php
   /* That's all, stop editing! Happy publishing. */
   ```

4. **ANTES de esa línea**, agrega estas líneas:
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   define('WP_DEBUG_DISPLAY', false);
   define('SCRIPT_DEBUG', true);
   ```

5. **Guarda el archivo**

6. **Intenta activar el plugin** nuevamente

7. **Revisa el archivo `wp-content/debug.log`** - Ahí verás el error exacto

### Ejemplo completo en wp-config.php:

```php
// ... código anterior ...

define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', true);

/* That's all, stop editing! Happy publishing. */
```

## 📋 Opción 2: Usar el Script de Debug

Si tienes acceso al servidor:

1. Copia el archivo `debug-activation.php` a la **raíz de WordPress** (donde está wp-config.php)

2. Accede desde tu navegador a:
   ```
   http://tudominio.com/debug-activation.php
   ```

3. Verás una página con toda la información de diagnóstico

## 📋 Opción 3: Ver Logs del Servidor

Si tienes acceso a los logs del servidor:

1. **En cPanel/Hosting:**
   - Ve a "Error Logs" o "Logs"
   - Busca los errores recientes cuando intentas activar el plugin

2. **En servidor con acceso SSH:**
   ```bash
   tail -f /var/log/apache2/error.log
   # o
   tail -f /var/log/php_errors.log
   ```

## 🔍 Qué Buscar en el Error

Cuando veas el error, compártelo conmigo. Los errores más comunes son:

- **"Class 'MTZ_Slider_Database' not found"**: Archivo faltante o ruta incorrecta
- **"Parse error"**: Error de sintaxis en algún archivo PHP
- **"Call to undefined function"**: Función de WordPress no disponible
- **"Maximum execution time exceeded"**: Problema de rendimiento o bucle infinito
- **"Cannot redeclare class"**: El plugin se está cargando dos veces

## 📝 Después de Ver el Error

Una vez que tengas el error completo del `debug.log`, compártelo conmigo y podré darte la solución exacta.

**IMPORTANTE**: Después de diagnosticar el problema, considera desactivar el debug en producción por seguridad:
```php
define('WP_DEBUG', false);
```

