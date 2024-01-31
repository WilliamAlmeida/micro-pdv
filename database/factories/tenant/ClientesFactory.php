<?php

namespace Database\Factories\Tenant;

use App\Models\Cidade;
use App\Models\Tenant;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ClientesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tenant = Tenant::has('convenios')->with(['convenios' => fn($q) => $q->take(1)])->inRandomOrder()->first();

        $company = fake()->company;
        $cidade  = Cidade::with('estado.pais')->inRandomOrder()->first();

        return [
            'tenant_id' => $tenant->id,
            'id_convenio' => $tenant->convenios->first()->id,
            'nome_fantasia' => $company,
            'slug' => Str::slug($company),
            'razao_social' => Str::upper($company),
            'idpais' => $cidade->estado->pais->id,
            'idestado' => $cidade->estado->id,
            'idcidade' => $cidade->id,
            'cnpj' => $this->faker->numerify('##.###.###/####-##'),
            'inscricao_estadual' => $this->faker->numerify('###.###.###.###'),
            'cpf' => $this->faker->numerify('###.###.###-##'),
            'end_logradouro' => $this->faker->streetName,
            'end_numero' => $this->faker->buildingNumber,
            'end_complemento' => $this->faker->secondaryAddress,
            'end_bairro' => $this->faker->citySuffix,
            'end_uf' => $this->faker->stateAbbr,
            'end_cidade' => $this->faker->city,
            'end_cep' => $this->faker->postcode,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
