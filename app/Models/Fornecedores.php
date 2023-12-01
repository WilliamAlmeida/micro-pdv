<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fornecedores extends Model
{
    protected $table = 'fornecedores';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    use SoftDeletes;

    /*Add your validation rules here*/
    public static $rules = array(
        'id_tipo_fornecedor' => array('min:0'),
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
        'id_tipo_fornecedor' => array('min:0'),
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
        'id_tipo_fornecedor', 'nome_fantasia', 'slug', 'razao_social', 'idpais', 'idestado', 'idcidade', 'cnpj', 'inscricao_estadual', 'cpf', 'end_logradouro', 'end_numero', 'end_complemento', 'end_bairro', 'end_cidade', 'end_uf', 'end_cep', 'whatsapp'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'id',
    ];

    public static $tipos_empresas = [
        [
            'name' => 'Restaurante', 'id' => 1, 'desc' => 'Restaurantes, Hamburguerias, Sorveterias, Marmitarias, Lanchonetes, etc.'
        ],
        [
            'name' => 'Mercado', 'id' => 2, 'desc' => 'Supermercados, Hortifrutis, Mercearias, etc.'
        ],
        [
            'name' => 'Bebidas', 'id' => 3, 'desc' => 'Distribuidoras, Adegas, Choperias, Casas de Cervejas, etc.'
        ],
        [
            'name' => 'Farmácia', 'id' => 4, 'desc' => 'Farmácias, Drogarias, Medicamentos Naturais, Perfumaria, etc.'
        ],
        [
            'name' => 'Pet Shop', 'id' => 5, 'desc' => 'Pets Shops, Casas de Ração, Venterinárias, etc.'
        ],
        [
            'name' => 'Vestuário', 'id' => 6, 'desc' => 'Loja de Roupa, Sapatos, etc.'
        ],
        [
            'name' => 'Eletrônicos', 'id' => 7, 'desc' => 'Loja de Celular, TV, Videogames, etc.'
        ],
        [
            'name' => 'Sorveteria', 'id' => 8, 'desc' => 'Açai, Sorvetes, etc.'
        ],
        [
            'name' => 'Outros', 'id' => 999, 'desc' => 'Outros.'
        ]
    ];

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

    public function endereco($withCep=false): string
    {
        $endereco = '';
        $endereco .= $this->end_logradouro;
        $endereco .= ', '.$this->end_numero;
        $endereco .= ', '.$this->end_bairro;
        $endereco .= ', '.$this->end_cidade;
        if($this->estado) $endereco .= ' - '.$this->estado->uf;
        if($withCep && $this->end_cep) $endereco .= $this->end_cep;
        return $endereco;
    }
}