<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Convenios extends Model
{
    protected $table = 'convenios';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    use SoftDeletes;

    /*Add your validation rules here*/
    public static $rules = array(
        'nome_fantasia' => array('required','min:0','max:255'),
        'slug' => array('min:0','max:255'),
        'razao_social' => array('min:0','max:255'),
        'idpais' => array('min:0','max:255'),
        'idestado' => array('max:255'),
        'idcidade' => array('max:255'),
        'cnpj' => array('min:0','max:18'),
        'inscricao_estadual' => array('min:0','max:20'),
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
        'nome_fantasia' => array('required','min:0','max:255'),
        'slug' => array('min:0','max:255'),
        'razao_social' => array('min:0','max:255'),
        'idpais' => array('min:0','max:255'),
        'idestado' => array('max:255'),
        'idcidade' => array('max:255'),
        'cnpj' => array('min:0','max:18'),
        'inscricao_estadual' => array('min:0','max:20'),
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
        'id_tipo_fornecedor', 'nome_fantasia', 'slug', 'razao_social', 'idpais', 'idestado', 'idcidade', 'cnpj', 'inscricao_estadual', 'end_logradouro', 'end_numero', 'end_complemento', 'end_bairro', 'end_cidade', 'end_uf', 'end_cep', 'whatsapp'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'id',
    ];

    public function pais()
    {
        return $this->hasOne('App\Model\Pais', 'id', 'pais_id');
    }

    public function estado()
    {
        return $this->hasOne('App\Model\Estado', 'id', 'idestado');
    }

    public function cidade()
    {
        return $this->hasOne('App\Model\Cidade', 'id', 'idcidade');
    }

    public function clientes()
    {
        return $this->hasMany('App\Model\Clientes', 'id_convenio', 'id');
    }
}