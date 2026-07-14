<?php

$dir = __DIR__ . '/app/Models';
$files = scandir($dir);

foreach ($files as $file) {
    if (strpos($file, '.php') !== false) {
        $path = $dir . '/' . $file;
        $content = file_get_contents($path);
        if (strpos($content, 'protected $guarded = [];') === false && strpos($content, '//') !== false) {
            $content = str_replace('//', 'protected $guarded = [];', $content);
            file_put_contents($path, $content);
        }
    }
}
echo "Models updated.";
