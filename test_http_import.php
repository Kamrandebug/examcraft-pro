<?php
/**
 * Test bulk import via HTTP POST request with proper session handling
 */

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

$jar = new CookieJar();

$client = new Client([
    'base_uri' => 'http://127.0.0.1:8000',
    'cookies' => $jar,
    'verify' => false,
]);

try {
    echo "🔐 Step 1: Get login page...\n";
    $response = $client->get('/login');
    $html = $response->getBody()->getContents();

    // Extract CSRF token
    preg_match('/<input[^>]*name="csrf_token"[^>]*value="([^"]*)"/', $html, $matches);
    $csrfToken = $matches[1] ?? null;

    if (!$csrfToken) {
        preg_match('/<input[^>]*name="_token"[^>]*value="([^"]*)"/', $html, $matches);
        $csrfToken = $matches[1] ?? null;
    }

    if (!$csrfToken) {
        die("❌ Could not extract CSRF token from login page\n");
    }

    echo "✓ CSRF token obtained\n";

    echo "\n🔐 Step 2: Login as admin...\n";
    $response = $client->post('/login', [
        'form_params' => [
            'email' => 'admin@examcraft.com',
            'password' => 'password',
            '_token' => $csrfToken,
        ],
        'allow_redirects' => true,
    ]);

    echo "✓ Logged in successfully\n";

    echo "\n📄 Step 3: Get bulk import form...\n";
    $response = $client->get('/admin/questions/bulk-import');
    $html = $response->getBody()->getContents();

    if (strpos($html, 'Import Questions from File') === false) {
        echo "❌ Bulk import page not found\n";
        echo "Page title: " . (preg_match('/<title>(.*?)<\/title>/', $html, $m) ? $m[1] : 'unknown');
        exit(1);
    }

    // Extract CSRF token from form
    preg_match('/<input[^>]*name="csrf_token"[^>]*value="([^"]*)"/', $html, $matches);
    $formCsrf = $matches[1] ?? null;

    if (!$formCsrf) {
        preg_match('/<input[^>]*name="_token"[^>]*value="([^"]*)"/', $html, $matches);
        $formCsrf = $matches[1] ?? null;
    }

    if (!$formCsrf) {
        die("❌ Could not extract CSRF token from form\n");
    }

    echo "✓ Form page loaded, CSRF token obtained\n";

    echo "\n📤 Step 4: Upload CSV file...\n";
    $response = $client->post('/admin/questions/bulk-import', [
        'multipart' => [
            [
                'name'     => '_token',
                'contents' => $formCsrf,
            ],
            [
                'name'     => 'file',
                'contents' => fopen('test_questions.csv', 'r'),
                'filename' => 'test_questions.csv',
            ],
            [
                'name'     => 'grade',
                'contents' => 'A Level',
            ],
            [
                'name'     => 'subject',
                'contents' => 'Chemistry',
            ],
        ],
        'allow_redirects' => true,
    ]);

    $html = $response->getBody()->getContents();
    $status = $response->getStatusCode();

    echo "Response status: $status\n";

    // Check for success message
    if (strpos($html, 'Successfully imported') !== false) {
        echo "\n✅ Import successful!\n";
        preg_match('/Successfully imported (\d+) questions/', $html, $matches);
        echo "   Imported: " . ($matches[1] ?? '?') . " questions\n";
    } elseif (strpos($html, 'Import Failed') !== false) {
        echo "\n❌ Import failed\n";
        // Extract error details
        if (preg_match('/<p>(.*?)<\/p>/s', $html, $matches)) {
            echo "   Error: " . strip_tags($matches[1]) . "\n";
        }
    } else {
        echo "\n⚠️  Unexpected response\n";
        if (strpos($html, 'Page Expired') !== false) {
            echo "   CSRF token mismatch (Page Expired)\n";
        } elseif (strpos($html, 'login') !== false) {
            echo "   Session lost, redirected to login\n";
        }
    }

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
