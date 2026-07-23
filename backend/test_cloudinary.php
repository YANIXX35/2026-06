<?php

require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$url = 'cloudinary://781417952128893:J7QxLiumsdDx_dWAHTXtBbZj-nA@drxkegfjv';
config(['filesystems.disks.cloudinary.url' => $url]);

try {
    file_put_contents('test_image.txt', 'dummy image content');
    $result = \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::uploadApi()->upload('test_image.txt', ['resource_type' => 'raw']);
    var_dump($result['secure_url']);
} catch (\Throwable $e) {
    echo 'Error: ' . $e->getMessage() . "\n" . $e->getTraceAsString();
}
