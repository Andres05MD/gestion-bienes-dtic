<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Ejecuta los seeders de la base de datos.
     */
    public function run(): void
    {
        // Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            // Permisos de bienes
            'ver bienes',
            'crear bienes',
            'editar bienes',
            'eliminar bienes',
            // Permisos de áreas
            'ver areas',
            'crear areas',
            'editar areas',
            'eliminar areas',
            // Permisos de estados
            'ver estados',
            'crear estados',
            'editar estados',
            'eliminar estados',
            // Permisos de categorías
            'ver categorias',
            'crear categorias',
            'editar categorias',
            'eliminar categorias',
            // Permisos de departamentos
            'ver departamentos',
            'crear departamentos',
            'editar departamentos',
            'eliminar departamentos',
            // Permisos de bienes externos
            'ver bienes externos',
            'crear bienes externos',
            'editar bienes externos',
            'eliminar bienes externos',
            // Permisos de usuarios
            'gestionar usuarios',
            'crear usuarios',
            'editar usuarios',
            'eliminar usuarios',
            // Permisos de transferencias internas
            'ver transferencias',
            'crear transferencias',
            'editar transferencias',
            'eliminar transferencias',
            // Permisos de desincorporaciones
            'ver desincorporaciones',
            'crear desincorporaciones',
            'editar desincorporaciones',
            'eliminar desincorporaciones',
            // Permisos de distribuciones de dirección
            'ver distribuciones',
            'crear distribuciones',
            'editar distribuciones',
            'eliminar distribuciones',
            // Permisos de estatus de actas
            'ver estatus actas',
            'crear estatus actas',
            'editar estatus actas',
            'eliminar estatus actas',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web']
            );
        }

        // Role: Operador — acceso global excepto usuarios
        $role1 = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'operador', 'guard_name' => 'web']);
        $operadorPermissions = [
            'ver bienes', 'crear bienes', 'editar bienes', 'eliminar bienes',
            'ver areas', 'crear areas', 'editar areas', 'eliminar areas',
            'ver estados', 'crear estados', 'editar estados', 'eliminar estados',
            'ver categorias', 'crear categorias', 'editar categorias', 'eliminar categorias',
            'ver departamentos', 'crear departamentos', 'editar departamentos', 'eliminar departamentos',
            'ver bienes externos', 'crear bienes externos', 'editar bienes externos', 'eliminar bienes externos',
            'ver transferencias', 'crear transferencias', 'editar transferencias', 'eliminar transferencias',
            'ver desincorporaciones', 'crear desincorporaciones', 'editar desincorporaciones', 'eliminar desincorporaciones',
            'ver distribuciones', 'crear distribuciones', 'editar distribuciones', 'eliminar distribuciones',
            'ver estatus actas', 'crear estatus actas', 'editar estatus actas', 'eliminar estatus actas',
        ];
        $role1->syncPermissions($operadorPermissions);

        // Role: Admin — todos los permisos
        $role2 = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $role2->givePermissionTo(\Spatie\Permission\Models\Permission::all());

        // Crear usuario admin por defecto
        $user = \App\Models\User::updateOrCreate(
            ['email' => 'admin@hospital.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('password'),
            ]
        );
        $user->assignRole($role2);

        // Crear usuario operador por defecto
        $userOp = \App\Models\User::updateOrCreate(
            ['email' => 'operador@hospital.com'],
            [
                'name' => 'Operador',
                'password' => bcrypt('password'),
            ]
        );
        $userOp->assignRole($role1);

        // Limpiar caché de nuevo
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
