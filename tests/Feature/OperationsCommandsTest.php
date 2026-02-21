<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\CategoriaBien;
use App\Models\Estado;
use App\Models\Departamento;
use App\Models\Area;
use App\Models\EstatusActa;
use App\Models\Bien;
use App\Models\BienExterno;

class OperationsCommandsTest extends TestCase
{
    public function test_creates_records_in_all_operation_modules_successfully()
    {
        $user = User::first();
        $this->assertNotNull($user, 'No existen usuarios en la DB. No se puede testear.');

        $categoria = CategoriaBien::first();
        $estado = Estado::first();
        $departamento = Departamento::where('nombre', 'not like', '%DTIC%')->first();
        $dtic = Departamento::where('nombre', 'like', '%DTIC%')->first();
        $area = Area::first();
        $estatusActa = EstatusActa::first();

        $this->assertNotNull($categoria, 'Faltan categorias');
        $this->assertNotNull($estado, 'Faltan estados');
        $this->assertNotNull($departamento, 'Faltan departamentos');
        $this->assertNotNull($dtic, 'Falta departamento DTIC');
        $this->assertNotNull($area, 'Faltan areas');
        $this->assertNotNull($estatusActa, 'Faltan estatus de acta');

        // Autenticar
        $this->actingAs($user);

        // 1. Distribuciones de Dirección
        $responseDist = $this->post(route('distribuciones-direccion.store'), [
            'numero_bien' => 'SIMULATED-' . rand(1000, 9999),
            'descripcion' => 'Bien Simulado',
            'serial' => 'SN-SIM-' . rand(100, 999),
            'categoria_bien_id' => $categoria->id,
            'estado_id' => $estado->id,
            'procedencia_id' => $departamento->id,
            'fecha' => now()->format('Y-m-d'),
        ]);

        $responseDist->assertSessionHasNoErrors();
        $responseDist->assertRedirect(route('distribuciones-direccion.index'));

        $bienExterno = BienExterno::latest('id')->first();
        $this->assertNotNull($bienExterno, 'El bien externo no se generó');

        // 2. Mantenimientos
        $responseMant = $this->post(route('mantenimientos.store'), [
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
        ]);

        $responseMant->assertSessionHasNoErrors();
        $responseMant->assertRedirect(route('mantenimientos.index'));

        // 3. Desincorporaciones
        $responseDesinc = $this->post(route('desincorporaciones.store'), [
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
            'numero_informe' => ['INF-SIMULADO-123'],
            'fecha' => now()->format('Y-m-d'),
        ]);

        $responseDesinc->assertSessionHasNoErrors();
        $responseDesinc->assertRedirect(route('desincorporaciones.index'));

        // 4. Transferencias Internas
        $bienDtic = Bien::first();
        if ($bienDtic) {
            $responseTrans = $this->post(route('transferencias-internas.store'), [
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
            ]);

            $responseTrans->assertSessionHasNoErrors();
            $responseTrans->assertRedirect(route('transferencias-internas.index'));
        }
    }
}
