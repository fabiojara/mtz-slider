# Script para crear release v2.3.0 en GitHub

$githubToken = "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"
$repoOwner = "fabiojara"
$repoName = "mtz-slider"
$version = "2.3.0"
$pluginDir = Get-Location

Write-Host "Creando release v$version..." -ForegroundColor Cyan

# Crear ZIP del plugin
Write-Host "Creando ZIP..." -ForegroundColor Yellow
$zipPath = Join-Path $env:TEMP "mtz-slider-$version.zip"
if (Test-Path $zipPath) {
    Remove-Item $zipPath -Force
}

$tempDir = Join-Path $env:TEMP "mtz-slider-release"
if (Test-Path $tempDir) {
    Remove-Item $tempDir -Recurse -Force
}
New-Item -ItemType Directory -Path $tempDir | Out-Null
$targetDir = Join-Path $tempDir "mtz-slider"
New-Item -ItemType Directory -Path $targetDir | Out-Null

Get-ChildItem -Path $pluginDir -Recurse -File | Where-Object {
    $file = $_
    $shouldInclude = $true

    if ($file.Name -like "*.zip" -or $file.Name -like "*.log" -or
        $file.Name -like "crear-zip*.ps1" -or $file.Name -eq "debug-activation.php" -or
        $file.Name -like "crear-release*.ps1" -or $file.Name -eq ".gitignore" -or
        $file.Name -eq "package.json" -or $file.Name -eq "package-lock.json" -or
        $file.Name -eq "vite.config.js" -or $file.Name -eq "composer.json" -or
        $file.Name -eq "composer.lock") {
        $shouldInclude = $false
    }

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

Compress-Archive -Path "$tempDir\mtz-slider" -DestinationPath $zipPath -Force
Remove-Item $tempDir -Recurse -Force

$zipSize = [math]::Round((Get-Item $zipPath).Length / 1MB, 2)
Write-Host "ZIP creado: $zipSize MB" -ForegroundColor Green

# Crear release
Write-Host "Creando release en GitHub..." -ForegroundColor Yellow

$releaseBody = "MTZ Slider v$version`n`nNuevas caracteristicas:`n- Sistema de efectos de animacion (Fade, Slide, Zoom, Flip, Cube 3D)`n- Selector de efectos en el panel de administracion`n- Contenido siempre centrado en todas las animaciones`n- Mejoras en deslizamiento tactil para moviles y tablets`n- Prevencion de scroll accidental durante swipe`n`nComo actualizar:`n- Actualiza desde WordPress Admin -> Plugins"

$releaseData = @{
    tag_name = "v$version"
    name = "MTZ Slider v$version"
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
    Write-Host "Release creado: v$version (ID: $releaseId)" -ForegroundColor Green
    Write-Host "URL: $($response.html_url)" -ForegroundColor Cyan

    # Subir ZIP
    Write-Host "Subiendo ZIP..." -ForegroundColor Yellow
    $uploadUrl = "https://uploads.github.com/repos/$repoOwner/$repoName/releases/$releaseId/assets?name=mtz-slider.zip"
    $zipBytes = [System.IO.File]::ReadAllBytes($zipPath)

    $uploadHeaders = @{
        Authorization = "Bearer $githubToken"
        Accept = "application/vnd.github.v3+json"
        "Content-Type" = "application/zip"
    }

    $uploadResponse = Invoke-RestMethod -Uri $uploadUrl -Method Post -Headers $uploadHeaders -Body $zipBytes
    Write-Host "ZIP subido exitosamente" -ForegroundColor Green
    Write-Host "URL de descarga: $($uploadResponse.browser_download_url)" -ForegroundColor Cyan

    Remove-Item $zipPath -Force

    Write-Host "`nRelease creado exitosamente!" -ForegroundColor Green
    Write-Host "El plugin detectara la actualizacion automaticamente en maximo 12 horas." -ForegroundColor Yellow
} catch {
    Write-Host "ERROR: $_" -ForegroundColor Red
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $responseBody = $reader.ReadToEnd()
        Write-Host "Detalles: $responseBody" -ForegroundColor Red
    }
}

