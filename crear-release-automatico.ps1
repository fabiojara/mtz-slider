# Script automatizado para crear releases en GitHub
# Ejecutar desde la carpeta del plugin: .\crear-release-automatico.ps1

param(
    [string]$nuevaVersion = "",
    [switch]$autoVersion = $false
)

# Configuracion
$githubToken = "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"
$repoOwner = "fabiojara"
$repoName = "mtz-slider"
$pluginDir = Get-Location

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  CREAR RELEASE AUTOMATICO - MTZ SLIDER" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# 1. Obtener version actual del plugin
Write-Host "1. Leyendo version actual..." -ForegroundColor Yellow
$pluginFile = Join-Path $pluginDir "mtz-slider.php"
if (!(Test-Path $pluginFile)) {
    Write-Host "ERROR: No se encuentra mtz-slider.php" -ForegroundColor Red
    exit 1
}

$content = Get-Content $pluginFile -Raw
if ($content -match "Version:\s*(\d+\.\d+\.\d+)") {
    $versionActual = $matches[1]
    Write-Host "   Version actual: $versionActual" -ForegroundColor Green
} else {
    Write-Host "ERROR: No se pudo leer la version del plugin" -ForegroundColor Red
    exit 1
}

# 2. Determinar nueva version
if ($autoVersion) {
    # Incrementar patch version (2.2.1 -> 2.2.2)
    $parts = $versionActual -split '\.'
    $major = [int]$parts[0]
    $minor = [int]$parts[1]
    $patch = [int]$parts[2]
    $patch++
    $nuevaVersion = "$major.$minor.$patch"
    Write-Host "2. Version automatica calculada: $nuevaVersion" -ForegroundColor Green
} elseif ($nuevaVersion -eq "") {
    Write-Host ""
    Write-Host "2. Nueva version (actual: $versionActual):" -ForegroundColor Yellow
    Write-Host "   Presiona ENTER para incrementar automaticamente o ingresa nueva version (ej: 2.2.2):" -ForegroundColor Gray
    $input = Read-Host
    if ($input -eq "") {
        $parts = $versionActual -split '\.'
        $major = [int]$parts[0]
        $minor = [int]$parts[1]
        $patch = [int]$parts[2]
        $patch++
        $nuevaVersion = "$major.$minor.$patch"
        Write-Host "   Usando version automatica: $nuevaVersion" -ForegroundColor Green
    } else {
        $nuevaVersion = $input
    }
}

# Validar formato de version
if ($nuevaVersion -notmatch '^\d+\.\d+\.\d+$') {
    Write-Host "ERROR: Formato de version invalido. Use formato: X.Y.Z (ej: 2.2.2)" -ForegroundColor Red
    exit 1
}

# Comparar versiones
if ([version]$nuevaVersion -le [version]$versionActual) {
    Write-Host "ERROR: La nueva version debe ser mayor que la actual ($versionActual)" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "3. Actualizando archivos con nueva version..." -ForegroundColor Yellow

# 3. Actualizar mtz-slider.php
$newContent = $content -replace "(Version:\s*)\d+\.\d+\.\d+", "`$1$nuevaVersion"
$newContent = $newContent -replace "(define\(['\`"]MTZ_SLIDER_VERSION['\`"],\s*['\`"])\d+\.\d+\.\d+", "`$1$nuevaVersion"
Set-Content -Path $pluginFile -Value $newContent -NoNewline
Write-Host "   ✓ mtz-slider.php actualizado" -ForegroundColor Green

# Actualizar package.json
$packageFile = Join-Path $pluginDir "package.json"
if (Test-Path $packageFile) {
    $packageContent = Get-Content $packageFile -Raw
    $packageContent = $packageContent -replace "(`"version`":\s*`")\d+\.\d+\.\d+", "`$1$nuevaVersion"
    Set-Content -Path $packageFile -Value $packageContent -NoNewline
    Write-Host "   ✓ package.json actualizado" -ForegroundColor Green
}

# 4. Crear ZIP del plugin
Write-Host ""
Write-Host "4. Creando ZIP del plugin..." -ForegroundColor Yellow
$zipPath = Join-Path $env:TEMP "mtz-slider-$nuevaVersion.zip"
if (Test-Path $zipPath) {
    Remove-Item $zipPath -Force
}

# Crear directorio temporal con estructura correcta
$tempDir = Join-Path $env:TEMP "mtz-slider-release"
if (Test-Path $tempDir) {
    Remove-Item $tempDir -Recurse -Force
}
New-Item -ItemType Directory -Path $tempDir | Out-Null
$targetDir = Join-Path $tempDir "mtz-slider"
New-Item -ItemType Directory -Path $targetDir | Out-Null

# Copiar archivos excluyendo los no necesarios
$exclude = @('node_modules', '.git', '.gitignore', 'vite.config.js', 'package.json', 'package-lock.json', 'composer.json', 'composer.lock', '*.zip', '*.log', 'crear-zip*.ps1', 'debug-activation.php', '.DS_Store', 'Thumbs.db')

Get-ChildItem -Path $pluginDir -Recurse -File | Where-Object {
    $file = $_
    $shouldInclude = $true

    # Verificar exclusiones
    foreach ($pattern in $exclude) {
        if ($file.Name -like $pattern) {
            $shouldInclude = $false
            break
        }
    }

    # Verificar rutas excluidas
    if ($file.FullName -match '\\node_modules\\' -or $file.FullName -match '\\\.git\\') {
        $shouldInclude = $false
    }

    return $shouldInclude
} | ForEach-Object {
    $relativePath = $_.FullName.Substring($pluginDir.FullName.Length + 1)
    $targetPath = Join-Path $targetDir $relativePath
    $targetFolder = Split-Path $targetPath -Parent

    if (!(Test-Path $targetFolder)) {
        New-Item -ItemType Directory -Path $targetFolder -Force | Out-Null
    }

    Copy-Item $_.FullName $targetPath -Force
}

# Comprimir
Compress-Archive -Path "$tempDir\mtz-slider" -DestinationPath $zipPath -Force
Remove-Item $tempDir -Recurse -Force

if (!(Test-Path $zipPath)) {
    Write-Host "ERROR: No se pudo crear el ZIP" -ForegroundColor Red
    exit 1
}

$zipSize = [math]::Round((Get-Item $zipPath).Length / 1MB, 2)
Write-Host "   ✓ ZIP creado: $zipSize MB" -ForegroundColor Green

# 5. Leer release notes si existe
Write-Host ""
Write-Host "5. Preparando release notes..." -ForegroundColor Yellow
$releaseNotesFile = Join-Path $pluginDir "release-$nuevaVersion.txt"
$releaseBody = "MTZ Slider v$nuevaVersion`n`n"

if (Test-Path $releaseNotesFile) {
    $releaseBody += Get-Content $releaseNotesFile -Raw
    Write-Host "   ✓ Usando release-$nuevaVersion.txt" -ForegroundColor Green
} else {
    $releaseBody += "Cambios en esta version:`n- Actualizaciones y mejoras`n`nComo actualizar:`n- Actualiza desde WordPress Admin -> Plugins"
    Write-Host "   ⚠ Archivo release-$nuevaVersion.txt no encontrado, usando texto generico" -ForegroundColor Yellow
}

# 6. Crear release en GitHub
Write-Host ""
Write-Host "6. Creando release en GitHub..." -ForegroundColor Yellow

$tagName = "v$nuevaVersion"
$releaseName = "MTZ Slider v$nuevaVersion"

$releaseData = @{
    tag_name = $tagName
    name = $releaseName
    body = $releaseBody
    draft = $false
    prerelease = $false
} | ConvertTo-Json

$headers = @{
    Authorization = "Bearer $githubToken"
    Accept = "application/vnd.github.v3+json"
}

try {
    $releaseUrl = "https://api.github.com/repos/$repoOwner/$repoName/releases"
    $response = Invoke-RestMethod -Uri $releaseUrl -Method Post -Headers $headers -Body $releaseData -ContentType "application/json"

    $releaseId = $response.id
    Write-Host "   ✓ Release creado: $tagName (ID: $releaseId)" -ForegroundColor Green
    Write-Host "   URL: $($response.html_url)" -ForegroundColor Cyan
} catch {
    Write-Host "ERROR al crear release: $_" -ForegroundColor Red
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $responseBody = $reader.ReadToEnd()
        Write-Host "Detalles: $responseBody" -ForegroundColor Red
    }
    Remove-Item $zipPath -Force
    exit 1
}

# 7. Subir ZIP como asset
Write-Host ""
Write-Host "7. Subiendo ZIP como asset..." -ForegroundColor Yellow

$uploadUrl = "https://uploads.github.com/repos/$repoOwner/$repoName/releases/$releaseId/assets?name=mtz-slider.zip"
$zipBytes = [System.IO.File]::ReadAllBytes($zipPath)

try {
    $uploadHeaders = @{
        Authorization = "Bearer $githubToken"
        Accept = "application/vnd.github.v3+json"
        "Content-Type" = "application/zip"
    }

    $uploadResponse = Invoke-RestMethod -Uri $uploadUrl -Method Post -Headers $uploadHeaders -Body $zipBytes
    Write-Host "   ✓ ZIP subido exitosamente" -ForegroundColor Green
    Write-Host "   URL de descarga: $($uploadResponse.browser_download_url)" -ForegroundColor Cyan
} catch {
    Write-Host "ERROR al subir ZIP: $_" -ForegroundColor Red
    Write-Host "   El release fue creado, pero el ZIP no se subio. Puedes subirlo manualmente." -ForegroundColor Yellow
}

# Limpiar
Remove-Item $zipPath -Force

# 8. Commit y push (opcional)
Write-Host ""
Write-Host "8. ¿Deseas hacer commit y push de los cambios? (S/N):" -ForegroundColor Yellow
$hacerCommit = Read-Host
if ($hacerCommit -eq "S" -or $hacerCommit -eq "s") {
    Write-Host "   Haciendo commit..." -ForegroundColor Yellow
    git add mtz-slider.php package.json
    git commit -m "Actualizar version a $nuevaVersion"
    git push origin master
    Write-Host "   ✓ Cambios subidos a GitHub" -ForegroundColor Green
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  RELEASE CREADO EXITOSAMENTE" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Version: $nuevaVersion" -ForegroundColor White
Write-Host "Tag: $tagName" -ForegroundColor White
Write-Host "Release: $($response.html_url)" -ForegroundColor Cyan
Write-Host ""
Write-Host "El plugin detectara la actualizacion automaticamente en:" -ForegroundColor Yellow
Write-Host "  - Maximo 12 horas (cache)" -ForegroundColor Gray
Write-Host "  - O inmediatamente si limpias el cache del plugin" -ForegroundColor Gray
Write-Host ""
Write-Host "Para limpiar el cache manualmente, ejecuta en WordPress:" -ForegroundColor Yellow
Write-Host '  delete_transient(''mtz_slider_latest_release'');' -ForegroundColor Gray
Write-Host ""

