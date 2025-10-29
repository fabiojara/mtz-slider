# Guía de Contribución - MTZ Slider

## 🚀 Configuración de GitHub

### 1. Crear el repositorio en GitHub

1. Ve a [GitHub](https://github.com) y crea un nuevo repositorio llamado `mtz-slider`
2. **NO** inicialices el repositorio con README, LICENSE o .gitignore (ya los tenemos)
3. Copia la URL del repositorio

### 2. Conectar el repositorio local con GitHub

Ejecuta los siguientes comandos en la terminal (dentro de la carpeta del plugin):

```bash
# Agregar el remoto de GitHub
git remote add origin https://github.com/TU-USUARIO/mtz-slider.git

# Cambiar el nombre de la rama principal (opcional, si usas main en lugar de master)
git branch -M main

# Subir los cambios a GitHub
git push -u origin main
```

O si estás usando master:

```bash
git remote add origin https://github.com/TU-USUARIO/mtz-slider.git
git push -u origin master
```

### 3. Actualizaciones futuras

Cada vez que hagas cambios, usa estos comandos para subirlos a GitHub:

```bash
# Ver el estado de los archivos
git status

# Agregar todos los cambios
git add .

# Hacer commit con un mensaje descriptivo
git commit -m "Descripción de los cambios"

# Subir los cambios a GitHub
git push
```

## 📝 Buenas Prácticas de Commit

Usa mensajes de commit descriptivos y en español:

```bash
git commit -m "Agregar soporte para navegación por teclado"
git commit -m "Corregir problema de responsive en móviles"
git commit -m "Mejorar UI del panel administrativo"
```

## 🌿 Trabajar con Ramas

Para trabajar en nuevas características sin afectar el código principal:

```bash
# Crear una nueva rama
git checkout -b feature/nueva-caracteristica

# Hacer cambios y commits...
git add .
git commit -m "Agregar nueva característica"

# Subir la rama a GitHub
git push -u origin feature/nueva-caracteristica
```

Luego puedes crear un Pull Request en GitHub.

## ✅ Checklist antes de hacer Push

- [ ] El código está funcionando correctamente
- [ ] No hay archivos temporales o sensibles en el repositorio
- [ ] El mensaje de commit es descriptivo
- [ ] Los cambios están probados

## 🔗 Recursos Adicionales

- [Documentación de Git](https://git-scm.com/doc)
- [Guía de GitHub](https://guides.github.com)
- [GitHub Flow](https://guides.github.com/introduction/flow/)

