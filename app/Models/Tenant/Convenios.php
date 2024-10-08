<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Stancl\Tenancy\Database\Concerns\HasScopedValidationRules;

class Convenios extends Model
{
    protected $table = 'convenios';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    use SoftDeletes, BelongsToTenant, HasFactory, HasScopedValidationRules;

    /*Add your validation rules here*/
    public static $rules = array(
        'tenant_id' => array('min:0'),
        'nome_fantasia' => array('required','min:0','max:255'),
        'slug' => array('min:0','max:255'),
        'razao_social' => array('min:0','max:255'),
        'idpais' => array('min:0','max:255'),
        'idestado' => array('max:255'),
        'idcidade' => array('max:255'),
        'cnpj' => array('min:0','max:18'),
        'inscricao_estadual' => array('min:0','max:20'),
        'cpf' => array('min:0','max:18'),
        'end_logradouro' => array('min:0','max:255'),
        'end_numero' => array('min:0','max:10'),
        'end_complemento' => array('min:0','max:255'),
        'end_bairro' => array('min:0','max:255'),
        'end_cidade' => array('min:0','max:255'),
        'end_uf' => array('min:0','max:255'),
        'end_cep' => array('min:0','max:14'),
        'whatsapp' => array('min:0'),
    );

    public static $rules_u = array(
        'tenant_id' => array('min:0'),
        'nome_fantasia' => array('required','min:0','max:255'),
        'slug' => array('min:0','max:255'),
        'razao_social' => array('min:0','max:255'),
        'idpais' => array('min:0','max:255'),
        'idestado' => array('max:255'),
        'idcidade' => array('max:255'),
        'cnpj' => array('min:0','max:18'),
        'inscricao_estadual' => array('min:0','max:20'),
        'cpf' => array('min:0','max:18'),
        'end_logradouro' => array('min:0','max:255'),
        'end_numero' => array('min:0','max:10'),
        'end_complemento' => array('min:0','max:255'),
        'end_bairro' => array('min:0','max:255'),
        'end_cidade' => array('min:0','max:255'),
        'end_uf' => array('min:0','max:255'),
        'end_cep' => array('min:0','max:14'),
        'whatsapp' => array('min:0'),
    );

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tenant_id', 'nome_fantasia', 'slug', 'razao_social', 'idpais', 'idestado', 'idcidade', 'cnpj', 'inscricao_estadual', 'cpf', 'end_logradouro', 'end_numero', 'end_complemento', 'end_bairro', 'end_cidade', 'end_uf', 'end_cep', 'whatsapp'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'id',
    ];

    public function empresas()
    {
        return $this->hasOne('App\Models\Tenant', 'id', 'tenant_id');
    }

    public function pais()
    {
        return $this->hasOne('App\Models\Pais', 'id', 'pais_id');
    }

    public function estado()
    {
        return $this->hasOne('App\Models\Estado', 'id', 'idestado');
    }

    public function cidade()
    {
        return $this->hasOne('App\Models\Cidade', 'id', 'idcidade');
    }

    public function clientes()
    {
        return $this->hasMany('App\Models\Tenant\Clientes', 'id_convenio', 'id');
    }
}