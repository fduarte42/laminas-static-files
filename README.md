# Laminas / Mezzio Static Files [![CI](https://github.com/fduarte42/laminas-static-files/actions/workflows/ci.yml/badge.svg)](https://github.com/fduarte42/laminas-static-files/actions/workflows/ci.yml)
A PSR-15 middleware that serves static assets for you

Example usage:
```php
$app->pipe('/fun-module/assets', new \Fduarte42\StaticFiles\StaticFilesMiddleware(
    __DIR__ . '/../vendor/fund-module/public',
    ['publicCachePath' => __DIR__ . '/../public/fun-module/assets']
));
```

