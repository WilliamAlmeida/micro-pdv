<?php

namespace Database\Factories\Tenant;

use App\Models\Cidade;
use App\Models\Tenant;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ConveniosFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $company = fake()->company;
        $cidade  = Cidade::with('estado.pais')->inRandomOrder()->first();

        return [
            'tenant_id' => Tenant::inRandomOrder()->first()->id,
            'nome_fantasia' => $company,
            'slug' => Str::slug($company),
            'razao_social' => Str::upper($company),
            'idpais' => $cidade->estado->pais->id,
            'idestado' => $cidade->estado->id,
            'idcidade' => $cidade->id,
            'cnpj' => fake()->numerify('##.###.###/####-##'),
            'inscricao_estadual' => fake()->numerify('###.###.###.###'),
            'cpf' => fake()->numerify('###.###.###-##'),
            'end_logradouro' => fake()->streetName,
            'end_numero' => fake()->buildingNumber,
            'end_complemento' => fake()->secondaryAddress,
            'end_bairro' => fake()->citySuffix,
            'end_uf' => fake()->stateAbbr,
            'end_cidade' => fake()->city,
            'end_cep' => fake()->postcode,
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
