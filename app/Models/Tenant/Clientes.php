<?php

namespace App\Models\Tenant;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Clientes extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    use SoftDeletes, BelongsToTenant, HasTenant;

    /*Add your validation rules here*/
    public static $rules = array(
        'empresas_id' => array('min:0'),
        'id_convenio' => array('min:0'),
        'nome_fantasia' => array('required','min:0','max:255'),
        'slug' => array('min:0','max:255'),
        'razao_social' => array('min:0','max:255'),
        'idpais' => array('min:0','max:255'),
        'idestado' => array('max:255'),
        'idcidade' => array('max:255'),
        'cnpj' => array('min:0','max:18'),
        'inscricao_estadual' => array('min:0','max:20'),
        'cpf' => array('min:0','max:14'),
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
        'empresas_id' => array('min:0'),
        'id_convenio' => array('min:0'),
        'nome_fantasia' => array('required','min:0','max:255'),
        'slug' => array('min:0','max:255'),
        'razao_social' => array('min:0','max:255'),
        'idpais' => array('min:0','max:255'),
        'idestado' => array('max:255'),
        'idcidade' => array('max:255'),
        'cnpj' => array('min:0','max:18'),
        'inscricao_estadual' => array('min:0','max:20'),
        'cpf' => array('min:0','max:14'),
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
        'empresas_id', 'id_convenio', 'nome_fantasia', 'slug', 'razao_social', 'idpais', 'idestado', 'idcidade', 'cnpj', 'inscricao_estadual', 'cpf', 'end_logradouro', 'end_numero', 'end_complemento', 'end_bairro', 'end_cidade', 'end_uf', 'end_cep', 'whatsapp'
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
        return $this->hasOne('App\Models\Tenant', 'id', 'empresas_id');
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

    public function convenio()
    {
        return $this->hasOne('App\Models\Tenant\Convenios', 'id', 'id_convenio');
    }

    public function compras()
    {
        return $this->hasMany('App\Models\Tenant\VendasHead','cliente_id','id');
    }
}