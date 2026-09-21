$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$php = Join-Path $projectRoot '..\.tools\php82\php.exe'
$publicPath = Join-Path $projectRoot 'public'

Push-Location $publicPath
try {
    & $php -S '127.0.0.1:8007' -t $publicPath (Join-Path $projectRoot 'vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php')
}
finally {
    Pop-Location
}
