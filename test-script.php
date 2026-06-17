<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Blog;

foreach (Blog::with('category')->get() as $b) {
    echo "Post: '{$b->title}' -> Category: " . ($b->category ? $b->category->name : 'NONE') . " (ID: {$b->category_id})\n";
}
