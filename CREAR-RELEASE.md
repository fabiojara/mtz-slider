# 🚀 Cómo crear releases en GitHub para actualizaciones automáticas

Para que WordPress detecte y permita actualizar el plugin automáticamente, necesitas crear releases en GitHub siguiendo estos pasos:

## 📋 Pasos para crear un release

### 1. Preparar el ZIP del plugin

Antes de crear el release, necesitas crear un ZIP del plugin completo:

```bash
# En Windows PowerShell (desde la carpeta wp-content/plugins)
Compress-Archive -Path mtz-slider\* -DestinationPath mtz-slider.zip -Force

# O usando Git Bash / Linux
cd wp-content/plugins
zip -r mtz-slider.zip mtz-slider/ -x "*.git*" "node_modules/*" "*.log"
```

**Importante**: El ZIP debe contener todos los archivos del plugin, **excepto**:
- `.git/`
- `node_modules/`
- Archivos de log
- `.env` o archivos sensibles

### 2. Crear el release en GitHub

1. Ve a tu repositorio en GitHub: https://github.com/fabiojara/mtz-slider
2. Haz clic en **"Releases"** (lado derecho, debajo de "About")
3. Haz clic en **"Create a new release"** o **"Draft a new release"**
4. Completa la información:
   - **Tag version**: Usa el formato `v2.2.1` (con la 'v' al inicio)
   - **Release title**: Por ejemplo "MTZ Slider v2.2.1"
   - **Description**: Copia el contenido del archivo `release-2.2.1.txt` o el changelog correspondiente
5. **Adjuntar el ZIP**:
   - Haz clic en **"Attach binaries"** o arrastra el archivo `mtz-slider.zip`
   - Es importante que el archivo se llame `mtz-slider.zip` o contenga el nombre del plugin
6. Haz clic en **"Publish release"**

### 3. Verificar que funcione

Después de crear el release:

1. Ve a tu WordPress → **Plugins → Plugins instalados**
2. Busca **MTZ Slider**
3. Si hay una nueva versión, verás un aviso de actualización
4. Haz clic en **"Actualizar ahora"**

## ⚙️ Configuración automática (Opcional)

Si prefieres usar `zipball_url` de GitHub (sin adjuntar ZIP manualmente), el sistema también funcionará, pero WordPress necesitará procesar la estructura extraída del ZIP de GitHub.

## 🔍 Solución de problemas

### El plugin no detecta actualizaciones

1. Verifica que el tag en GitHub tenga el formato correcto: `v2.2.1`
2. Verifica que hayas publicado el release (no solo el tag)
3. Espera unos minutos - WordPress cachea las actualizaciones por 12 horas
4. Puedes limpiar el caché manualmente:
   ```php
   delete_transient('mtz_slider_latest_release');
   ```

### Error al actualizar

1. Verifica que el ZIP tenga la estructura correcta (debe contener el archivo `mtz-slider.php` en la raíz)
2. Verifica los permisos del servidor
3. Revisa los logs de WordPress en `wp-content/debug.log`

## 📝 Ejemplo de release

**Tag**: `v2.2.1`
**Title**: `MTZ Slider v2.2.1`
**Description**:
```
MTZ Slider v2.2.1

Cambios
- Actualización de documentación (README y ACTUALIZACION.md) y metadatos del proyecto.
- Sin cambios funcionales en el código.

Cómo actualizar
- Desactiva y activa el plugin para asegurar que no queden cachés.
- Limpia caché del navegador (Ctrl/Cmd+F5).
```

## 🎯 Recomendación

Para la mejor experiencia, siempre adjunta el ZIP del plugin como archivo binario en el release de GitHub. Esto garantiza que WordPress pueda instalar la actualización sin problemas.

