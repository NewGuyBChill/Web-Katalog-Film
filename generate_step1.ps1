$dirs = @(
    "app\Middleware",
    "app\Shared\Helpers", "app\Shared\Traits", "app\Shared\Constants", "app\Shared\Exceptions", "app\Shared\Interfaces",
    "app\Services\Cache", "app\Services\Auth", "app\Services\Upload", "app\Services\Review", "app\Services\Rating",
    "app\Services\Watchlist", "app\Services\Notification", "app\Services\Recommendation", "app\Services\Activity"
)

foreach ($dir in $dirs) {
    if (-not (Test-Path "c:\xampp\htdocs\kinema\Web-Katalog-Film\$dir")) {
        New-Item -ItemType Directory -Force -Path "c:\xampp\htdocs\kinema\Web-Katalog-Film\$dir"
    }
}

$files = @(
    "app\Core\Model.php", "app\Core\Cache.php", "app\Core\Validator.php", "app\Core\Auth.php",
    "app\Middleware\AuthMiddleware.php", "app\Middleware\GuestMiddleware.php", "app\Middleware\AdminMiddleware.php", "app\Middleware\CSRFMiddleware.php", "app\Middleware\RateLimitMiddleware.php",
    "app\Shared\Helpers\ImageHelper.php", "app\Shared\Helpers\DateHelper.php", "app\Shared\Helpers\SlugHelper.php", "app\Shared\Helpers\RatingHelper.php", "app\Shared\Helpers\NumberHelper.php",
    "app\Shared\Traits\ApiResponseTrait.php", "app\Shared\Traits\UploadTrait.php", "app\Shared\Traits\CacheTrait.php",
    "app\Shared\Constants\Roles.php", "app\Shared\Constants\Genres.php", "app\Shared\Constants\NotificationType.php", "app\Shared\Constants\WatchlistStatus.php",
    "app\Shared\Exceptions\TMDBException.php", "app\Shared\Exceptions\ValidationException.php", "app\Shared\Exceptions\UploadException.php", "app\Shared\Exceptions\AuthenticationException.php",
    "app\Shared\Interfaces\CacheInterface.php", "app\Shared\Interfaces\UploadInterface.php"
)

foreach ($file in $files) {
    $path = "c:\xampp\htdocs\kinema\Web-Katalog-Film\$file"
    if (-not (Test-Path $path)) {
        New-Item -ItemType File -Force -Path $path
        
        $parts = $file.Split('\')
        if ($parts.Length -ge 3) {
            $ns = "App\" + $parts[1]
            if ($parts.Length -ge 4) {
                $ns += "\" + $parts[2]
            }
        } else {
            $ns = "App\Core"
        }
        
        $className = $parts[-1].Replace('.php', '')
        
        $type = "class"
        if ($file -match "Interfaces") { $type = "interface" }
        if ($file -match "Traits") { $type = "trait" }
        
        $content = "<?php`n`nnamespace $ns;`n`n$type $className`n{`n    // TODO: Implement`n}`n"
        Set-Content -Path $path -Value $content
    }
}
