<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\CategoriaBien;
use App\Models\Estado;
use App\Models\Departamento;
use App\Models\Area;
use App\Models\EstatusActa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SimulateOperationsCommand extends Command
{
    protected $signature = 'test:operations';
    protected $description = 'Simulate creating records in all operation modules to catch errors.';

    public function handle()
    {
        $this->info("Simulando creación de registros en todos los módulos...");

        $user = User::first();
        if (!$user) {
            $this->error("No hay usuarios en la BD.");
            return;
        }

        // Get dependencies
        $categoria = CategoriaBien::first();
        $estado = Estado::first();
        $departamento = Departamento::where('nombre', 'not like', '%DTIC%')->first();
        $dtic = Departamento::where('nombre', 'like', '%DTIC%')->first();
        $area = Area::first();
        $estatusActa = EstatusActa::first();

        if (!$categoria || !$estado || !$departamento || !$dtic || !$area || !$estatusActa) {
            $this->error("Faltan datos básicos en la BD (Categorías, Estados, etc.).");
            return;
        }

        $this->info("Usuario de prueba: {$user->email}");

        // 1. Distribución de Dirección
        $this->info("Probando: Distribuciones de Dirección...");
        $distData = [
            'numero_bien' => 'SIMULATED-' . rand(1000, 9999),
            'descripcion' => 'Bien Simulado',
            'serial' => 'SN-SIM-' . rand(100, 999),
            'categoria_bien_id' => $categoria->id,
            'estado_id' => $estado->id,
            'procedencia_id' => $departamento->id,
            'fecha' => now()->format('Y-m-d'),
        ];
        $this->simulateRequest('distribuciones-direccion.store', 'POST', $distData, $user);

        // Fetch the newly created external asset
        $bienExterno = \App\Models\BienExterno::latest('id')->first();

        // 2. Mantenimiento (Requiere Bien Externo)
        $this->info("Probando: Mantenimientos...");
        $mantenimientoData = [
            'procedencia_id' => $dtic->id,
            'destino_id' => $departamento->id,
            'modo_mantenimiento' => 'individual',
            'bienes' => [
                [
                    'id' => $bienExterno->id,
                    'tipo' => 'externo',
                    'numero_bien' => $bienExterno->numero_bien,
                    'descripcion' => 'Simulado',
                ]
            ],
            'descripcion' => 'Mantenimiento Preventivo Simulado',
            'estatus_acta_id' => $estatusActa->id,
            'fecha' => now()->format('Y-m-d'),
            'fecha_firma' => now()->format('Y-m-d'),
        ];
        $this->simulateRequest('mantenimientos.store', 'POST', $mantenimientoData, $user);

        // 3. Desincorporaciones (Múltiple, Bien Externo)
        $this->info("Probando: Desincorporaciones...");
        $desincData = [
            'procedencia_id' => $dtic->id,
            'destino_id' => $departamento->id,
            'modo_desincorporacion' => 'individual',
            'bienes' => [
                [
                    'id' => $bienExterno->id,
                    'tipo' => 'externo',
                    'numero_bien' => $bienExterno->numero_bien,
                    'descripcion' => 'Simulado',
                ]
            ],
            'motivo' => 'OBSOLESCENCIA',
            'numero_informe' => 'INF-SIMULADO-123',
            'estatus_acta_id' => $estatusActa->id,
            'fecha' => now()->format('Y-m-d'),
        ];
        $this->simulateRequest('desincorporaciones.store', 'POST', $desincData, $user);

        // 4. Transferencias Internas
        $bienDtic = \App\Models\Bien::first();
        if ($bienDtic) {
            $this->info("Probando: Transferencias Internas...");
            $transData = [
                'modo_transferencia' => 'individual',
                'bienes' => [
                    [
                        'id' => $bienDtic->id,
                        'tipo' => 'dtic',
                        'numero_bien' => $bienDtic->numero_bien,
                        'descripcion' => 'Dtic Simulado',
                    ]
                ],
                'procedencia_id' => $dtic->id,
                'area_procedencia_id' => $area->id,
                'destino_id' => $departamento->id,
                'estatus_acta_id' => $estatusActa->id,
                'fecha' => now()->format('Y-m-d'),
                'fecha_firma' => now()->format('Y-m-d')
            ];
            $this->simulateRequest('transferencias-internas.store', 'POST', $transData, $user);
        }

        $this->info("Todas las simulaciones enviadas.");
    }

    private function simulateRequest($routeName, $method, $data, $user)
    {
        try {
            $url = route($routeName);
        } catch (\Exception $e) {
            $this->error("Ruta no encontrada: $routeName");
            return;
        }

        $request = Request::create($url, $method, $data);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        Auth::login($user);

        // Bypass CSRF by mocking a token
        $token = 'dummy-token';
        $request->setLaravelSession(app('session')->driver());
        $request->session()->start();
        $request->session()->put('_token', $token);
        $request->merge(['_token' => $token]);
        $request->headers->set('X-CSRF-TOKEN', $token);

        $response = app()->handle($request);

        if ($response->getStatusCode() >= 500) {
            $this->error("Error {$response->getStatusCode()} en $routeName!");
            if (isset($response->exception) && $response->exception) {
                $this->error($response->exception->getMessage());
                $this->error($response->exception->getFile() . ':' . $response->exception->getLine());
            } else {
                // If it's a redirect indicating failure or standard 500 content
                $this->error(substr($response->getContent(), 0, 500));
            }
        } elseif ($response->isSuccessful() || $response->isRedirection()) {
            if (session()->has('errors')) {
                $this->warn("Errores de validación en $routeName:");
                foreach (session('errors')->all() as $error) {
                    $this->warn(" - $error");
                }
            } else {
                $this->info("Éxito al solicitar $routeName (Código: {$response->getStatusCode()})");
            }
        } else {
            $this->warn("Código {$response->getStatusCode()} al solicitar $routeName");
        }
    }
}
