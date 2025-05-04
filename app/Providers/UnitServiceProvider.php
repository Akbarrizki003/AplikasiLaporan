<?php

namespace App\Providers;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;

class UnitServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Event::listen(Registered::class, function (Registered $event) {
            $user = $event->user;
            
            // Jika user baru berperan sebagai unit, buat entri unit kosong
            if ($user->role === 'unit') {
                // Cek apakah sudah memiliki unit atau belum
                if (!$user->unit) {
                    $unit = new Unit();
                    $unit->id_user = $user->id;
                    $unit->nama_unit = ''; // Nilai default kosong
                    $unit->direktur = '';  // Nilai default kosong
                    $unit->telepon = '';   // Nilai default kosong
                    $unit->save();
                }
            }
        });
    }
}