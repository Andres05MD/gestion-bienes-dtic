<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Desincorporacion;
use App\Models\TransferenciaInterna;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class ShareNotifications
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $hoy = Carbon::now();
            $diasCriticos = 15;

            // Contar Desincorporaciones pendientes críticas
            $desincCount = Desincorporacion::whereDoesntHave('estatusActa', function ($query) {
                    $query->where('nombre', 'Actas Listas');
                })
                ->whereDate('fecha', '<=', $hoy->copy()->subDays($diasCriticos))
                ->count();

            // Contar Transferencias pendientes críticas
            $transfCount = TransferenciaInterna::whereDoesntHave('estatusActa', function ($query) {
                    $query->where('nombre', 'Actas Listas');
                })
                ->whereDate('fecha', '<=', $hoy->copy()->subDays($diasCriticos))
                ->count();

            $totalAlertas = $desincCount + $transfCount;

            View::share('totalAlertas', $totalAlertas);
        }

        return $next($request);
    }
}
