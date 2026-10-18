<?php

namespace App\Providers;

use App\Models\Klaim;
use App\Models\LaporanHilang;
use App\Models\LaporanTemuan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Relation;
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
        Carbon::setLocale('id');

        Relation::morphMap([
            'laporan_hilang' => LaporanHilang::class,
            'laporan_temuan' => LaporanTemuan::class,
            'klaim' => Klaim::class,
        ]);
    }
}
