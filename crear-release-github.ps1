# Script para crear un Release en GitHub con el ZIP completo adjunto
$ErrorActionPreference = "Stop"

$repo = "fabiojara/mtz-slider"
$version = "2.3.5"
$tag = "v$version"

# Obtener token desde archivo local o variable de entorno
$tokenFile = Join-Path (Get-Location) ".git-token-config.md"
if (Test-Path $tokenFile) {
    $tokenContent = Get-Content $tokenFile -Raw
    if ($tokenContent -match 'ghp_[A-Za-z0-9]+') {
        $token = $matches[0]
    } else {
        Write-Host "Error: No se encontro token en $tokenFile" -ForegroundColor Red
        Write-Host "Crea el archivo .git-token-config.md con el token o define la variable de entorno GITHUB_TOKEN" -ForegroundColor Yellow
        exit 1
    }
} elseif ($env:GITHUB_TOKEN) {
    $token = $env:GITHUB_TOKEN
} else {
    Write-Host "Error: No se encontro token de GitHub" -ForegroundColor Red
    Write-Host "Define la variable de entorno GITHUB_TOKEN o crea el archivo .git-token-config.md" -ForegroundColor Yellow
    exit 1
}

# Ruta del ZIP local
$zipPath = Join-Path (Get-Location) "mtz-slider-v2.3.5-completo.zip"

if (-not (Test-Path $zipPath)) {
    Write-Host "Error: No se encontro el archivo ZIP: $zipPath" -ForegroundColor Red
    Write-Host "Ejecuta primero: .\crear-zip-final.ps1" -ForegroundColor Yellow
    exit 1
}

$zipSize = (Get-Item $zipPath).Length / 1MB
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Creando Release en GitHub v$version" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "`nZIP: $zipPath" -ForegroundColor Yellow
Write-Host "Tamano: $([math]::Round($zipSize, 2)) MB" -ForegroundColor Yellow

# Leer el ZIP
Write-Host "`nLeyendo ZIP..." -ForegroundColor Yellow
$zipBytes = [System.IO.File]::ReadAllBytes($zipPath)

# Crear el Release
$releaseBody = @"
## MTZ Slider v$version - Version Completa

Este release incluye todos los archivos necesarios para instalar el plugin desde cero.

### Archivos incluidos:
- Todos los archivos PHP del plugin
- Archivos CSS (admin.css, public.css)
- Archivos JavaScript (admin.js, public.js)
- Todas las carpetas: admin, includes, public, assets
- Documentacion completa

### Instalacion:
1. Descarga el archivo ZIP adjunto
2. Ve a WordPress Admin -> Plugins -> Anadir nuevo
3. Haz clic en `"Subir plugin`"
4. Selecciona el ZIP descargado
5. Activa el plugin

### Cambios en esta version:
- Mejoras en compatibilidad con Elementor
- Mejora en deteccion de shortcodes
- Optimizacion de inicializacion de sliders
- Correcciones en visualizacion de imagenes e iconos

Ver ACTUALIZACION.md para detalles completos.
"@

$releaseData = @{
    tag_name = $tag
    name = "MTZ Slider v$version"
    body = $releaseBody
    draft = $false
    prerelease = $false
} | ConvertTo-Json -Depth 10

$headers = @{
    "Authorization" = "token $token"
    "Accept" = "application/vnd.github.v3+json"
}

Write-Host "`nCreando Release en GitHub..." -ForegroundColor Yellow

try {
    $releaseUrl = "https://api.github.com/repos/$repo/releases"
    $response = Invoke-RestMethod -Uri $releaseUrl -Method Post -Headers $headers -Body $releaseData -ContentType "application/json"

    $releaseId = $response.id
    Write-Host "Release creado: $($response.html_url)" -ForegroundColor Green

    # Subir el ZIP como asset
    Write-Host "`nSubiendo archivo ZIP..." -ForegroundColor Yellow
    $uploadUrl = "https://uploads.github.com/repos/$repo/releases/$releaseId/assets?name=mtz-slider-v$version-completo.zip"

    $uploadHeaders = @{
        "Authorization" = "token $token"
        "Accept" = "application/vnd.github.v3+json"
        "Content-Type" = "application/zip"
    }

    $uploadResponse = Invoke-RestMethod -Uri $uploadUrl -Method Post -Headers $uploadHeaders -Body $zipBytes -ContentType "application/zip"

    Write-Host "ZIP subido exitosamente" -ForegroundColor Green
    Write-Host "`n========================================" -ForegroundColor Cyan
    Write-Host "RELEASE CREADO EXITOSAMENTE" -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Cyan
    Write-Host "`nURL del Release: $($response.html_url)" -ForegroundColor Yellow
    Write-Host "URL de descarga del ZIP: $($uploadResponse.browser_download_url)" -ForegroundColor Yellow

} catch {
    Write-Host "`nERROR al crear Release:" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    if ($_.ErrorDetails) {
        Write-Host $_.ErrorDetails.Message -ForegroundColor Red
    }
    exit 1
}
