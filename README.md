# Laminas / Mezzio Static Files [![Build Status](https://api.travis-ci.com/fduarte42/laminas-static-files.svg?branch=master&status=passed)](https://app.travis-ci.com/github/fduarte42/laminas-static-files)
A PSR-15 middleware that serves static assets for you

Example usage:
```php
$app->pipe('/fun-module/assets', new \Fduarte42\StaticFiles\StaticFilesMiddleware(
    __DIR__ . '/../vendor/fund-module/public',
    ['publicCachePath' => __DIR__ . '/../public/fun-module/assets']
));
```

