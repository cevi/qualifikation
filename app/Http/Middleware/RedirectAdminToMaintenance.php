<?php

namespace App\Http\Middleware;

use App\Services\PendingMigrations;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Nach einem Update mit offenen Migrationen fehlen Tabellen oder Spalten, die der neue Code schon abfragt, und die
 * Übersicht nach dem Login stürzt ab. Admins landen deshalb auf der Wartungsseite, bis die Migrationen gelaufen sind.
 * Andere Personen leitet sie nicht um; sie können ohnehin nichts tun.
 */
class RedirectAdminToMaintenance
{
    public function __construct(private PendingMigrations $migrations) {}

    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('GET') && ! $request->routeIs('admin.maintenance')
            && Auth::user()?->isAdmin() && $this->migrations->any()) {
            return redirect()->route('admin.maintenance');
        }

        return $next($request);
    }
}
