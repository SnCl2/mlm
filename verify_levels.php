<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

// Simulate logged in user 21
$userId = 21;
$user = User::find($userId);

if (!$user) {
    echo "User $userId not found.\n";
    exit;
}
Auth::login($user);

echo "Testing tree for User: {$user->name} ({$user->id})\n";

$controller = new DashboardController();

// table() uses 16 depth.
// We'll also try a larger depth just in case.
$depth = 16;
echo "Building with depth: $depth\n";
$tree = $controller->buildBinaryTree($userId, $depth);

if (!$tree) {
    echo "Tree build returned null.\n";
    exit;
}

// Flatten and count levels
$flat = [];
flatten($tree, $flat);

$counts = array_count_values(array_column($flat, 'level'));
ksort($counts);

echo "Level Counts:\n";
foreach ($counts as $level => $count) {
    echo "Level $level: $count nodes\n";
}
echo "Max Level found: " . max(array_keys($counts)) . "\n";


// Also try with greater depth to see if more exists
$depth2 = 30;
echo "\nBuilding with depth: $depth2\n";
$tree2 = $controller->buildBinaryTree($userId, $depth2);
$flat2 = [];
flatten($tree2, $flat2);
$counts2 = array_count_values(array_column($flat2, 'level'));
echo "Max Level found with deep scan: " . max(array_keys($counts2)) . "\n";


function flatten($node, &$result = [], $level = 0)
{
    if (!$node) return;
    $item = $node;
    unset($item['children']); // don't duplicate
    $item['level'] = $level;
    $result[] = $item;

    if (!empty($node['children'])) {
        foreach ($node['children'] as $child) {
            flatten($child, $result, $level + 1);
        }
    }
}
