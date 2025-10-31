# Script para crear ZIP correcto del plugin
# Ejecutar desde la carpeta mtz-slider

$sourceDir = Get-Location
$outputFile = Join-Path $sourceDir.Parent "mtz-slider-INSTALAR.zip"

Write-Host "Creando ZIP desde: $sourceDir"
Write-Host "Destino: $outputFile"

# Eliminar ZIP anterior
if (Test-Path $outputFile) {
    Remove-Item $outputFile -Force
}

# Archivos y carpetas a excluir
$exclude = @('node_modules', '.git', '.gitignore', 'vite.config.js', 'package.json', 'package-lock.json', 'composer.json', 'composer.lock', 'crear-zip*.ps1', 'debug-activation.php', '*.zip', '*.log')

# Obtener todos los archivos
$allFiles = Get-ChildItem -Path $sourceDir -Recurse -File

# Filtrar archivos
$filesToZip = $allFiles | Where-Object {
    $file = $_
    $shouldInclude = $true

    # Verificar exclusiones por nombre
    foreach ($pattern in $exclude) {
        if ($file.Name -like $pattern) {
            $shouldInclude = $false
            break
        }
    }

    # Verificar exclusiones por ruta
    if ($file.FullName -match '\\node_modules\\' -or $file.FullName -match '\\\.git\\') {
        $shouldInclude = $false
    }

    return $shouldInclude
}

Write-Host "Archivos a incluir: $($filesToZip.Count)"

# Crear ZIP usando Compress-Archive de forma correcta
$filesToZip | ForEach-Object {
    $relativePath = $_.FullName.Substring($sourceDir.FullName.Length + 1)
    Compress-Archive -Path $_.FullName -DestinationPath $outputFile -Update -CompressionLevel Optimal
}

if (Test-Path $outputFile) {
    $size = [math]::Round((Get-Item $outputFile).Length / 1MB, 2)
    Write-Host ""
    Write-Host "ZIP creado exitosamente!"
    Write-Host "Archivo: $outputFile"
    Write-Host "Tamanio: $size MB"
    Write-Host ""
    Write-Host "IMPORTANTE: Verifica que el ZIP tenga esta estructura al descomprimirlo:"
    Write-Host "  mtz-slider/"
    Write-Host "  mtz-slider.php"
    Write-Host "  includes/"
    Write-Host "  class-mtz-slider-database.php"
} else {
    Write-Host "ERROR: No se pudo crear el ZIP"
}

