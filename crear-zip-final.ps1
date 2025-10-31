# Script para crear ZIP completo del plugin
$source = Get-Location
$zipName = "mtz-slider-v2.3.5-completo.zip"
$zipPath = Join-Path $source $zipName

# Eliminar ZIP anterior
if (Test-Path $zipPath) {
    Remove-Item $zipPath -Force
}

# Directorio temporal
$tempDir = Join-Path $env:TEMP "mtz-slider-build"
if (Test-Path $tempDir) {
    Remove-Item $tempDir -Recurse -Force
}
$pluginDir = Join-Path $tempDir "mtz-slider"
New-Item -ItemType Directory -Path $pluginDir -Force | Out-Null

Write-Host "Copiando archivos..." -ForegroundColor Yellow

# Copiar carpetas principales
$folders = @('admin', 'includes', 'public', 'assets')
foreach ($folder in $folders) {
    $src = Join-Path $source $folder
    if (Test-Path $src) {
        Copy-Item -Path $src -Destination "$pluginDir\$folder" -Recurse -Force
        Write-Host "  Copiado: $folder" -ForegroundColor Green
    }
}

# Copiar archivos principales
$files = @(
    'mtz-slider.php',
    'package.json',
    'composer.json',
    'vite.config.js',
    'LICENSE',
    'README.md',
    'ACTUALIZACION.md',
    'INSTALL.md',
    'CONTRIBUTING.md',
    'USO.md',
    'HABILITAR-DEBUG.md',
    'SOLUCION-ERROR-FATAL.md',
    'ESTRUCTURA-CORRECTA.md'
)

foreach ($file in $files) {
    $src = Join-Path $source $file
    if (Test-Path $src) {
        Copy-Item $src -Destination "$pluginDir\$file" -Force
        Write-Host "  Copiado: $file" -ForegroundColor Green
    }
}

# Copiar archivos release
Get-ChildItem -Path $source -Filter "release-*.txt" | ForEach-Object {
    Copy-Item $_.FullName -Destination "$pluginDir\$($_.Name)" -Force
    Write-Host "  Copiado: $($_.Name)" -ForegroundColor Green
}

Write-Host "`nCreando ZIP..." -ForegroundColor Yellow
Compress-Archive -Path $pluginDir -DestinationPath $zipPath -Force -CompressionLevel Optimal

$size = (Get-Item $zipPath).Length / 1MB
Write-Host "`nZIP creado: $zipName" -ForegroundColor Green
Write-Host "Tamaño: $([math]::Round($size, 2)) MB" -ForegroundColor Green
Write-Host "Ubicación: $zipPath" -ForegroundColor Cyan

# Limpiar
Remove-Item $tempDir -Recurse -Force

Write-Host "`nListo para instalar!" -ForegroundColor Green

