<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Bien;
use App\Models\BienExterno;
use App\Models\Departamento;
use App\Models\EstatusActa;
use App\Models\TransferenciaInterna;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransferenciaInternaLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_transfer_internal_asset_to_external_department_updates_area()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $areaOrigen = Area::factory()->create(['nombre' => 'Area Origen']);
        $bien = Bien::factory()->create(['area_id' => $areaOrigen->id]);
        
        $departamentoDestino = Departamento::factory()->create(['nombre' => 'Departamento Destino']);
        $estatus = EstatusActa::factory()->create();

        $response = $this->post(route('transferencias-internas.store'), [
            'numero_bien' => '123',
            'descripcion' => 'Test Bien',
            'fecha' => now()->toDateString(),
            'estatus_acta_id' => $estatus->id,
            'bien_id' => $bien->id,
            'procedencia_id' => null, // DTIC origen
            'destino_id' => $departamentoDestino->id,
        ]);

        $response->assertRedirect(route('transferencias-internas.index'));

        // Verify Area was created
        $newArea = Area::where('nombre', 'Departamento Destino')->first();
        $this->assertNotNull($newArea);
        
        // Verify Bien area updated
        $this->assertEquals($newArea->id, $bien->refresh()->area_id);
    }

    public function test_transfer_internal_asset_to_dtic_updates_area()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $areaOrigen = Area::factory()->create(['nombre' => 'Area Origen']); // Simulating external as area just for test setup
        $bien = Bien::factory()->create(['area_id' => $areaOrigen->id]);
        
        $areaDestino = Area::factory()->create(['nombre' => 'Almacén DTIC']);
        $estatus = EstatusActa::factory()->create();
        $departamentoProcedencia = Departamento::factory()->create();

        $response = $this->post(route('transferencias-internas.store'), [
            'numero_bien' => '123',
            'descripcion' => 'Test Bien',
            'fecha' => now()->toDateString(),
            'estatus_acta_id' => $estatus->id,
            'bien_id' => $bien->id,
            'procedencia_id' => $departamentoProcedencia->id,
            'destino_id' => null, // DTIC Destino
            'area_id' => $areaDestino->id,
        ]);

        $response->assertRedirect(route('transferencias-internas.index'));

        // Verify Bien area updated
        $this->assertEquals($areaDestino->id, $bien->refresh()->area_id);
    }

    public function test_transfer_external_asset_to_external_department_updates_department()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $departamentoOrigen = Departamento::factory()->create();
        $bienExterno = BienExterno::factory()->create(['departamento_id' => $departamentoOrigen->id]);
        
        $departamentoDestino = Departamento::factory()->create();
        $estatus = EstatusActa::factory()->create();

        $response = $this->post(route('transferencias-internas.store'), [
            'numero_bien' => '123',
            'descripcion' => 'Test Bien Externo',
            'fecha' => now()->toDateString(),
            'estatus_acta_id' => $estatus->id,
            'bien_externo_id' => $bienExterno->id,
            'procedencia_id' => $departamentoOrigen->id,
            'destino_id' => $departamentoDestino->id,
        ]);

        $response->assertRedirect(route('transferencias-internas.index'));

        // Verify BienExterno department updated
        $this->assertEquals($departamentoDestino->id, $bienExterno->refresh()->departamento_id);
    }
}
