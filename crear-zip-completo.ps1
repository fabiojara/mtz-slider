# Script para crear ZIP completo del plugin MTZ Slider
# Crea un ZIP con todos los archivos necesarios para instalación

$ErrorActionPreference = "Stop"

# Directorio actual (donde está el script)
$sourceDir = Get-Location
$version = "2.3.5"
$zipName = "mtz-slider-completo-v$version.zip"
$tempDir = Join-Path $env:TEMP "mtz-slider-build-$([System.Guid]::NewGuid().ToString())"
$pluginDir = Join-Path $tempDir "mtz-slider"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Creando ZIP completo de MTZ Slider v$version" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

try {
    # Crear directorio temporal
    Write-Host "`n1. Creando directorio temporal..." -ForegroundColor Yellow
    if (Test-Path $tempDir) {
        Remove-Item $tempDir -Recurse -Force
    }
    New-Item -ItemType Directory -Path $pluginDir -Force | Out-Null
    Write-Host "   ✓ Directorio creado" -ForegroundColor Green

    # Lista de archivos a incluir
    Write-Host "`n2. Copiando archivos del plugin..." -ForegroundColor Yellow
    $filesCopied = 0

    # Copiar archivo principal
    $mainFiles = @(
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

    foreach ($file in $mainFiles) {
        $source = Join-Path $sourceDir $file
        if (Test-Path $source) {
            Copy-Item $source -Destination $pluginDir -Force
            $filesCopied++
            Write-Host "   ✓ $file" -ForegroundColor Green
        }
    }

    # Copiar archivos release-*.txt
    Get-ChildItem -Path $sourceDir -Filter "release-*.txt" | ForEach-Object {
        Copy-Item $_.FullName -Destination $pluginDir -Force
        $filesCopied++
        Write-Host "   ✓ $($_.Name)" -ForegroundColor Green
    }

    # Copiar carpetas principales
    $folders = @(
        'admin',
        'includes',
        'public',
        'assets'
    )

    foreach ($folder in $folders) {
        $sourceFolder = Join-Path $sourceDir $folder
        if (Test-Path $sourceFolder) {
            $destFolder = Join-Path $pluginDir $folder
            Copy-Item -Path $sourceFolder -Destination $destFolder -Recurse -Force -Exclude @('*.map', 'package-lock.json', '.DS_Store')

            # Contar archivos copiados
            $count = (Get-ChildItem -Path $destFolder -Recurse -File).Count
            $filesCopied += $count
            Write-Host "   ✓ Carpeta '$folder' ($count archivos)" -ForegroundColor Green
        }
    }

    Write-Host "`n   Total de archivos copiados: $filesCopied" -ForegroundColor Cyan

    # Verificar estructura
    Write-Host "`n3. Verificando estructura del plugin..." -ForegroundColor Yellow
    $requiredFiles = @(
        "$pluginDir\mtz-slider.php",
        "$pluginDir\includes\class-mtz-slider-database.php",
        "$pluginDir\includes\class-mtz-slider-api.php",
        "$pluginDir\admin\views\admin-page.php",
        "$pluginDir\public\views\slider.php",
        "$pluginDir\assets\css\public.css",
        "$pluginDir\assets\js\public.js"
    )

    $allPresent = $true
    foreach ($file in $requiredFiles) {
        if (Test-Path $file) {
            Write-Host "   ✓ $(Split-Path $file -Leaf)" -ForegroundColor Green
        } else {
            Write-Host "   ✗ FALTA: $(Split-Path $file -Leaf)" -ForegroundColor Red
            $allPresent = $false
        }
    }

    if (-not $allPresent) {
        throw "Faltan archivos requeridos en el ZIP"
    }

    # Crear ZIP
    Write-Host "`n4. Creando archivo ZIP..." -ForegroundColor Yellow
    $zipPath = Join-Path $sourceDir $zipName

    # Eliminar ZIP existente
    if (Test-Path $zipPath) {
        Remove-Item $zipPath -Force
        Write-Host "   ✓ ZIP anterior eliminado" -ForegroundColor Green
    }

    # Crear ZIP desde el directorio temporal
    Compress-Archive -Path "$tempDir\mtz-slider" -DestinationPath $zipPath -Force -CompressionLevel Optimal

    $zipSize = (Get-Item $zipPath).Length / 1MB
    Write-Host "   ✓ ZIP creado: $zipName" -ForegroundColor Green
    Write-Host "   ✓ Tamaño: $([math]::Round($zipSize, 2)) MB" -ForegroundColor Green

    Write-Host "`n========================================" -ForegroundColor Cyan
    Write-Host "✓ ZIP COMPLETO CREADO EXITOSAMENTE" -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Cyan
    Write-Host "`nUbicación: $zipPath" -ForegroundColor Yellow
    Write-Host "`nEste ZIP está listo para instalar en WordPress" -ForegroundColor Green

} catch {
    Write-Host "`n✗ ERROR: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
} finally {
    # Limpiar directorio temporal
    if (Test-Path $tempDir) {
        Write-Host "`n5. Limpiando archivos temporales..." -ForegroundColor Yellow
        Remove-Item $tempDir -Recurse -Force -ErrorAction SilentlyContinue
        Write-Host "   ✓ Limpieza completada" -ForegroundColor Green
    }
}
