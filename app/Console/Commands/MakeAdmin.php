<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class MakeAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user interactively';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating a new Admin user...');

        $name = $this->ask('Full Name');
        $email = $this->ask('Email Address');
        $password = $this->secret('Password');
        $passwordConfirmation = $this->secret('Confirm Password');

        if ($password !== $passwordConfirmation) {
            $this->error('Passwords do not match!');
            return 1;
        }

        if (User::where('email', $email)->exists()) {
            $this->error('User with this email already exists!');
            return 1;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        // Verificar si existe el rol admin, si no, informar o intentar crearlo (aunque deberia existir por el seeder)
        // El seeder usa 'admin' como nombre del rol.
        if (!Role::where('name', 'admin')->exists()) {
            $this->warn('Role "admin" does not exist. Please run the seeder first: php artisan db:seed --class=RolesAndPermissionsSeeder');
            // Opcionalmente podríamos crearlo aquí, pero respetar los seeders es mejor práctica.
            if ($this->confirm('Do you want to create the "admin" role now?', true)) {
                 Role::create(['name' => 'admin']);
            } else {
                 $this->error('User created but role "admin" not assigned.');
                 return 1;
            }
        }

        $user->assignRole('admin');

        $this->info("User {$user->name} ({$user->email}) created and assigned 'admin' role successfully!");
        
        return 0;
    }
}
