<?php

namespace App\Traits;

use App\Models\Tenant;

trait HasTenant
{
    protected $_tenant_colmun = 'tenant_id';

    public function scopeWithTenant($query, int $sameTenant = 1, int|Tenant $empresa = null)
    {
        $empresa = is_null($empresa) ? auth()->user()->empresas_id : (is_int($empresa) ? $empresa : $empresa->id);

        $column = $query->from.'.'.(isset($this->tenant_column) ? $this->tenant_column : $this->_tenant_colmun);

        return $query->where($column, ($sameTenant ? '=' : '!='), $empresa);
    }
}
