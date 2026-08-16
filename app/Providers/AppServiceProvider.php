<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->cleanupStaleViteHotFile();
    }

    /**
     * The `public/hot` file is written by `npm run dev` (Vite) and tells Laravel
     * to load assets from the Vite dev server. If the dev server is stopped but
     * the file is left behind, `@vite` points at a dead port and the app renders
     * a blank screen. Remove it whenever it no longer points at a live server,
     * so `@vite` falls back to the compiled `public/build` assets automatically.
     */
    private function cleanupStaleViteHotFile(): void
    {
        $hotFile = public_path('hot');

        if (! is_file($hotFile)) {
            return;
        }

        $url = trim((string) file_get_contents($hotFile));

        if ($url === '' || ! $this->isViteDevServerAlive($url)) {
            @unlink($hotFile);
        }
    }

    /**
     * Check whether the Vite dev server referenced by the hot file is actually
     * listening. A single fast socket probe is enough; it only runs while the
     * hot file is present (i.e. during/after a dev session, not in production).
     */
    private function isViteDevServerAlive(string $url): bool
    {
        $parts = parse_url($url);
        $host = $parts['host'] ?? '127.0.0.1';
        $port = $parts['port'] ?? 80;

        $socket = @fsockopen($host, $port, $errno, $errstr, 0.3);

        if ($socket === false) {
            return false;
        }

        fclose($socket);

        return true;
    }
}
