<?php
/**
 * Test script for bulk question import
 * Tests the QuestionImportService with a real CSV file
 */

require 'vendor/autoload.php';

use App\Services\QuestionImportService;
use App\Models\QuestionBank;
use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Authenticate as admin user
$admin = User::where('email', 'admin@examcraft.com')->first();
if (!$admin) {
    echo "❌ Admin user not found. Please seed the database first.\n";
    exit(1);
}

auth()->login($admin);
echo "✓ Authenticated as: {$admin->email} (ID: {$admin->id})\n";

// Create a mock uploaded file
$filePath = 'test_import.csv';
if (!file_exists($filePath)) {
    echo "❌ Test CSV file not found at $filePath\n";
    exit(1);
}

// Use Symfony's UploadedFile to simulate upload
$file = new \Symfony\Component\HttpFoundation\File\UploadedFile(
    $filePath,
    'test_import.csv',
    'text/csv',
    null,
    true
);

echo "\n📂 File: {$file->getClientOriginalName()} ({$file->getSize()} bytes)\n";

// Test the import service
$service = new QuestionImportService();
$result = $service->import($file, 'O Level', 'Physics');

echo "\n📊 Import Result:\n";
echo "  Success: " . ($result['success'] ? '✓ Yes' : '✗ No') . "\n";
echo "  Message: {$result['message']}\n";
echo "  Imported: {$result['imported']}\n";
echo "  Failed: {$result['failed']}\n";
echo "  Total: {$result['total']}\n";

if (!empty($result['errors'])) {
    echo "\n❌ Errors:\n";
    foreach ($result['errors'] as $error) {
        echo "  - Row {$error['line']}: {$error['error']}\n";
    }
}

// Verify questions were created
$count = QuestionBank::where('grade', 'O Level')
    ->where('subject', 'Physics')
    ->count();

echo "\n✓ Total Physics questions in O Level: $count\n";

// Show a sample question
$sample = QuestionBank::where('grade', 'O Level')
    ->where('subject', 'Physics')
    ->latest()
    ->first();

if ($sample) {
    echo "\n📝 Sample Imported Question:\n";
    echo "  ID: {$sample->id}\n";
    echo "  Stem: " . substr($sample->data['stem_text'], 0, 80) . "...\n";
    echo "  Options: " . count($sample->data['options']) . "\n";
    echo "  Correct Answer: {$sample->data['correct_answer']}\n";
}

echo "\n✅ Test completed.\n";
