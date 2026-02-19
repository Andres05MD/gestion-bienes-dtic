<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Imports\BienesImport;
use Illuminate\Support\Str;

class BienesImportTest extends TestCase
{
    /** @test */
    public function test_it_parses_numero_bien_correctly()
    {
        $import = new BienesImport();
        
        // Reflexión para acceder al método privado
        $method = new \ReflectionMethod(BienesImport::class, 'parseNumeroBien');
        $method->setAccessible(true);

        // Test BN
        $result = $method->invoke($import, 'BN.123:456');
        $this->assertEquals('BIEN NACIONAL', $result['categoria']);
        $this->assertEquals('123456', $result['numero']);

        // Test BE
        $result = $method->invoke($import, 'BE-789');
        $this->assertEquals('BIEN ESTADAL', $result['categoria']);
        $this->assertEquals('789', $result['numero']);

        // Test UCLA
        $result = $method->invoke($import, 'UCLA1020');
        $this->assertEquals('BIEN UCLA', $result['categoria']);
        $this->assertEquals('1020', $result['numero']);

        // Test C/I
        $result = $method->invoke($import, 'C/I 555');
        $this->assertEquals('CONTROL INTERNO', $result['categoria']);
        $this->assertEquals('555', $result['numero']);

        // Test S/B
        $result = $method->invoke($import, 'S/B');
        $this->assertNull($result['categoria']);
        $this->assertEquals('S/N', $result['numero']);

        // Test Vacío
        $result = $method->invoke($import, '');
        $this->assertNull($result['categoria']);
        $this->assertEquals('S/N', $result['numero']);
    }
}
