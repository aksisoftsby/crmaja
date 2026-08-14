<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteReferenceIntegrityTest extends TestCase
{
    public function test_all_static_blade_route_references_exist(): void
    {
        $missing = [];
        $referenceCount = 0;

        foreach (File::allFiles(resource_path('views')) as $file) {
            preg_match_all("/(?<!->)\\broute\\(\\s*['\"]([A-Za-z0-9_.-]+)['\"]/", $file->getContents(), $matches);
            foreach ($matches[1] as $routeName) {
                if ($routeName === 'register' && ! Route::has('register')) {
                    continue;
                }

                $referenceCount++;
                if (! Route::has($routeName)) {
                    $missing[] = $file->getRelativePathname().': '.$routeName;
                }
            }
        }

        $this->assertGreaterThan(0, $referenceCount, 'Tidak ada referensi route() Blade yang terdeteksi.');
        $this->assertSame([], $missing, "Referensi rute Blade tidak tersedia:\n".implode("\n", $missing));
    }
}
