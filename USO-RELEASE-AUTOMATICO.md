# 🚀 Guía de Uso del Script Automatizado de Releases

## 📋 Descripción

El script `crear-release-automatico.ps1` automatiza todo el proceso de crear un release en GitHub:

1. ✅ Lee la versión actual del plugin
2. ✅ Calcula o solicita la nueva versión
3. ✅ Actualiza los archivos con la nueva versión
4. ✅ Crea el ZIP con la estructura correcta
5. ✅ Crea el release en GitHub
6. ✅ Sube el ZIP como asset del release
7. ✅ Opcionalmente hace commit y push

## 🎯 Cómo Usar

### Opción 1: Versión Automática (Recomendado)

Ejecuta el script sin parámetros y presiona ENTER cuando pida la versión:

```powershell
cd c:\laragon\www\variospluginswp\wp-content\plugins\mtz-slider
.\crear-release-automatico.ps1
```

El script:
- Lee la versión actual (ej: `2.2.1`)
- Incrementa automáticamente la versión patch (`2.2.2`)
- Te pregunta si estás de acuerdo

### Opción 2: Especificar Versión Manualmente

```powershell
.\crear-release-automatico.ps1 -nuevaVersion "2.3.0"
```

### Opción 3: Incremento Automático Silencioso

```powershell
.\crear-release-automatico.ps1 -autoVersion
```

Esto incrementa automáticamente sin preguntar.

## 📝 Ejemplo Completo

```powershell
# 1. Ir a la carpeta del plugin
cd c:\laragon\www\variospluginswp\wp-content\plugins\mtz-slider

# 2. Ejecutar el script
.\crear-release-automatico.ps1

# 3. El script te mostrará:
#    - Versión actual: 2.2.1
#    - Nueva versión sugerida: 2.2.2
#    - Presiona ENTER para aceptar o escribe otra versión

# 4. El script:
#    - Actualiza mtz-slider.php con la nueva versión
#    - Actualiza package.json
#    - Crea el ZIP con estructura correcta
#    - Crea el release en GitHub
#    - Sube el ZIP

# 5. Opcionalmente te pregunta si quieres hacer commit y push
```

## ⚙️ Qué Hace el Script

### 1. Actualización de Versión
- Actualiza `Version:` en el header del plugin
- Actualiza `MTZ_SLIDER_VERSION` en `mtz-slider.php`
- Actualiza `version` en `package.json`

### 2. Creación del ZIP
- Crea un ZIP con estructura correcta:
  ```
  mtz-slider.zip
  └── mtz-slider/
      ├── mtz-slider.php
      ├── includes/
      └── ...
  ```
- Excluye archivos innecesarios (`node_modules`, `.git`, etc.)

### 3. Release en GitHub
- Crea el release con el tag `v2.2.2` (formato correcto)
- Usa el archivo `release-2.2.2.txt` si existe, o genera uno genérico
- Sube el ZIP como asset del release

### 4. Commit (Opcional)
- Te pregunta si quieres hacer commit y push de los cambios
- Si aceptas, hace commit de los archivos actualizados y los sube a GitHub

## 📋 Preparación Antes de Crear un Release

### 1. Crear Archivo de Release Notes (Opcional pero Recomendado)

Crea un archivo `release-X.X.X.txt` con el changelog:

```
MTZ Slider v2.2.2

Cambios
- Corrección de manejo de rutas y mejoras en activación
- Mejoras en sistema de actualizaciones automáticas
- Actualización de documentación

Cómo actualizar
- Actualiza desde WordPress Admin -> Plugins
- Limpia caché del navegador después de actualizar
```

### 2. Verificar Cambios

Antes de crear el release:
- ✅ Todos los cambios están guardados
- ✅ No hay errores en el código
- ✅ Has probado el plugin localmente
- ✅ Tienes el archivo `release-X.X.X.txt` listo (opcional)

## 🔧 Solución de Problemas

### Error: "No se encuentra mtz-slider.php"
**Solución**: Asegúrate de ejecutar el script desde la carpeta del plugin.

### Error: "Formato de versión inválido"
**Solución**: Usa el formato `X.Y.Z` (ej: `2.2.2`, no `2.2` ni `v2.2.2`).

### Error: "La nueva versión debe ser mayor"
**Solución**: Asegúrate de que la nueva versión sea mayor que la actual. Ej: si tienes `2.2.1`, usa `2.2.2` o superior.

### Error al crear release en GitHub
**Posibles causas**:
- Token de GitHub inválido o expirado
- El tag ya existe en GitHub
- Problemas de conexión

**Solución**: 
- Verifica que el token esté correcto en el script
- Si el tag ya existe, elimínalo desde GitHub o usa otra versión

### El ZIP no se sube
**Solución**: El release se crea igual. Puedes subir el ZIP manualmente desde la página del release en GitHub.

## 💡 Consejos

1. **Siempre crea el archivo release-X.X.X.txt** antes de ejecutar el script para tener changelogs completos
2. **Haz commit y push** cuando el script lo pregunte para mantener el repositorio actualizado
3. **Verifica el release** en GitHub después de crearlo para asegurarte de que todo está correcto
4. **Prueba la actualización** en un sitio de desarrollo antes de publicar en producción

## 📅 Flujo de Trabajo Recomendado

1. Hacer cambios en el plugin
2. Probar localmente
3. Crear archivo `release-X.X.X.txt` con los cambios
4. Ejecutar `.\crear-release-automatico.ps1`
5. Aceptar la nueva versión sugerida
6. Aceptar hacer commit y push
7. Verificar el release en GitHub
8. Esperar a que WordPress detecte la actualización (máximo 12 horas)

## 🎉 Resultado

Después de ejecutar el script:
- ✅ Versión actualizada en el código
- ✅ ZIP creado con estructura correcta
- ✅ Release publicado en GitHub
- ✅ ZIP adjunto al release
- ✅ Usuarios podrán actualizar automáticamente desde WordPress

¡Todo listo para que tus usuarios reciban las actualizaciones automáticamente!

