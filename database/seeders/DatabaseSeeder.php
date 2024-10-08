<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'William',
            'email' => 'williamkillerca@hotmail.com',
            'password' => '12345678',
            'type' => 2,
        ]);

        $this->call(PaisesSeeder::class);
        $this->call(EstadosSeeder::class);
        $this->call(CidadesSeeder::class);

        $this->call(NcmsSeeder::class);
        $this->call(CfopsSeeder::class);
        $this->call(CestsSeeder::class);

        $this->call(TenantSeeder::class);

        $this->call(RolesAndPermissionsSeeder::class);

        // $this->call(ConveniosSeeder::class);
        // $this->call(ClientesSeeder::class);

        // $this->call(ResetDadosCaixa::class);
        // $this->call(ResetEmpresas::class);
    }
}
