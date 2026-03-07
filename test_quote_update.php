<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Quote;

$quote = Quote::first();
if ($quote) {
    echo "Original category: " . $quote->quote_category_id . "\n";
    $newCategoryId = ($quote->quote_category_id == 1) ? 2 : 1;
    $quote->update(['quote_category_id' => $newCategoryId]);
    $quote->refresh();
    echo "Updated category: " . $quote->quote_category_id . "\n";
} else {
    echo "No quote found\n";
}
