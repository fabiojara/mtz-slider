# Script para crear ZIP de producción del plugin MTZ Slider
# Ejecutar desde la carpeta del plugin: .\crear-zip-produccion.ps1

$pluginDir = Get-Location
$zipPath = Join-Path $pluginDir.Parent "mtz-slider-production.zip"

# Archivos y carpetas a excluir
$excludePatterns = @(
    ".git",
    ".gitignore",
    "node_modules",
    "*.log",
    "*.zip",
    ".env",
    ".DS_Store",
    "Thumbs.db",
    "vite.config.js",
    "package.json",
    "package-lock.json",
    "composer.json",
    "composer.lock",
    "crear-zip-produccion.ps1",
    ".vscode",
    ".idea"
)

Write-Host "Creando ZIP de producción..." -ForegroundColor Green
Write-Host "Directorio del plugin: $pluginDir" -ForegroundColor Cyan
Write-Host "Destino del ZIP: $zipPath" -ForegroundColor Cyan

# Eliminar ZIP anterior si existe
if (Test-Path $zipPath) {
    Remove-Item $zipPath -Force
    Write-Host "ZIP anterior eliminado." -ForegroundColor Yellow
}

# Crear lista de archivos a incluir
$filesToInclude = Get-ChildItem -Path $pluginDir -Recurse | Where-Object {
    $shouldInclude = $true

    # Verificar si debe excluirse
    foreach ($pattern in $excludePatterns) {
        if ($_.FullName -match [regex]::Escape($pattern) -or $_.Name -like $pattern) {
            $shouldInclude = $false
            break
        }

        # Verificar si está en una carpeta excluida
        if ($_.FullName -like "*\$pattern\*") {
            $shouldInclude = $false
            break
        }
    }

    # Excluir archivos ocultos (que empiezan con punto excepto index.php)
    if ($_.Name -match '^\.' -and $_.Name -ne '.htaccess') {
        $shouldInclude = $false
    }

    return $shouldInclude
}

Write-Host "`nArchivos a incluir:" -ForegroundColor Green
$filesToInclude | Select-Object -First 10 | ForEach-Object { Write-Host "  - $($_.FullName.Replace($pluginDir.FullName, '.'))" }

if ($filesToInclude.Count -gt 10) {
    Write-Host "  ... y $($filesToInclude.Count - 10) archivos más" -ForegroundColor Gray
}

# Crear el ZIP
try {
    # Crear un archivo temporal para el ZIP
    $tempZip = [System.IO.Path]::GetTempFileName()
    Remove-Item $tempZip -Force

    # Usar .NET para crear el ZIP
    Add-Type -Assembly System.IO.Compression.FileSystem
    $zip = [System.IO.Compression.ZipFile]::Open($tempZip, [System.IO.Compression.ZipArchiveMode]::Create)

    foreach ($file in $filesToInclude) {
        $relativePath = $file.FullName.Substring($pluginDir.FullName.Length + 1)
        $entry = $zip.CreateEntry($relativePath)
        $entryStream = $entry.Open()
        $fileStream = [System.IO.File]::OpenRead($file.FullName)
        $fileStream.CopyTo($entryStream)
        $fileStream.Close()
        $entryStream.Close()
    }

    $zip.Dispose()

    # Mover el ZIP temporal al destino final
    Move-Item $tempZip $zipPath -Force

    Write-Host "`n✓ ZIP creado exitosamente: $zipPath" -ForegroundColor Green
    Write-Host "Tamaño: $([math]::Round((Get-Item $zipPath).Length / 1MB, 2)) MB" -ForegroundColor Cyan

} catch {
    Write-Host "`n✗ Error al crear el ZIP: $_" -ForegroundColor Red
    exit 1
}

Write-Host "`nListo! El ZIP esta listo para instalar en WordPress." -ForegroundColor Green

