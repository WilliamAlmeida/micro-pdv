<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Str;
use App\Models\Scopes\TenantScope;
use Stancl\Tenancy\Contracts\Tenant;

/**
 * @property-read Tenant $tenant
 */
trait BelongsToManyTenant
{
    /**
    * Defines a belongsToMany relationship based on a tenant model.
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
    */
    public function tenants()
    {
        // Check if the $tenant_relation_model property has not been previously defined.
        if (!$this->tenant_relation_model) {
            // Get the table name of the current model (assuming the table name is singular).
            $current_table = Str::singular($this->getTable());

            // Get the table name of the configured tenant model.
            $tenant_table = Str::singular(app()->make(config('tenancy.tenant_model'))->getTable(), 1);

            // Concatenate these two table names to form the relationship table name.
            // This creates a name composed of the current model's table name and the tenant model's table name, separated by an underscore.
            $this->tenant_relation_model = $current_table . '_' . $tenant_table;
        }

        // Return the belongsToMany relationship using the configured tenant model and the generated relationship table name.
        return $this->belongsToMany(config('tenancy.tenant_model'), $this->tenant_relation_model);
    }

    public static function bootBelongsToManyTenant()
    {
        static::addGlobalScope(new TenantScope);

        // static::creating(function ($model) {
            // if (! $model->getAttribute(BelongsToManyTenant::$tenantIdColumn) && ! $model->relationLoaded('tenant')) {
            //     if (tenancy()->initialized) {
            //         $model->setAttribute(BelongsToManyTenant::$tenantIdColumn, tenant()->getTenantKey());
            //         $model->setRelation('tenant', tenant());
            //     }
            // }

            // Verifica se a tabela de junção user_tenant está sendo utilizada pelo relacionamento.
            // if ($model->getTable() === 'tenants') {
            //     // Verifica se a coluna tenant_id não está definida e se a relação 'tenant' não foi carregada.
            //     if (! $model->getAttribute(BelongsToManyTenant::$tenantIdColumn) && ! $model->relationLoaded('tenant')) {
            //         // Verifica se o sistema de inquilinos foi inicializado.
            //         if (tenancy()->initialized) {
            //             // Define o tenant_id com o valor do inquilino atual e carrega a relação 'tenant'.
            //             $model->setAttribute(BelongsToManyTenant::$tenantIdColumn, tenant()->getTenantKey());
            //             $model->setRelation('tenant', tenant());
            //         }
            //     }
            // }
        // });
    }
}
