# Script simple para crear ZIP del plugin para instalacion
$source = Get-Location
$dest = Join-Path $source.Parent "mtz-slider-completo.zip"

if (Test-Path $dest) {
    Remove-Item $dest -Force
}

# Archivos a excluir
$excludeNames = @('.git', 'node_modules', '.gitignore', 'vite.config.js', 'package.json', 'package-lock.json', 'composer.json', 'composer.lock', 'crear-zip-produccion.ps1', 'crear-zip-install.ps1', 'debug-activation.php')
$excludeExtensions = @('.log', '.zip')

# Obtener todos los archivos y directorios
$items = Get-ChildItem -Path $source -Recurse

# Filtrar
$filesToInclude = $items | Where-Object {
    $item = $_

    # Excluir si es directorio excluido
    if ($item.PSIsContainer) {
        return $item.Name -notin $excludeNames -and $item.FullName -notmatch '\\\.git\\' -and $item.FullName -notmatch '\\node_modules\\'
    }

    # Excluir si es archivo excluido
    if ($item.Name -in $excludeNames) { return $false }
    if ($item.Extension -in $excludeExtensions) { return $false }
    if ($item.FullName -match '\\\.git\\') { return $false }
    if ($item.FullName -match '\\node_modules\\') { return $false }

    return $true
}

# Crear ZIP
$filesToInclude | ForEach-Object {
    $relativePath = $_.FullName.Substring($source.FullName.Length + 1)
    Compress-Archive -Path $_.FullName -DestinationPath $dest -Update -CompressionLevel Optimal
}

$sizeMB = [math]::Round((Get-Item $dest).Length / 1MB, 2)
Write-Host "ZIP creado: $dest"
Write-Host "Tamanio: $sizeMB MB"

