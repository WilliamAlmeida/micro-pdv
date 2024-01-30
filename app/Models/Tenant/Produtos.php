<?php

namespace App\Models\Tenant;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Produtos extends Model
{
    protected $table = 'produtos';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    use SoftDeletes, BelongsToTenant, HasTenant;

    /*Add your validation rules here*/
    public static $rules = array(
        'tenant_id' => array('required','min:1'),
        'titulo' => array('required','min:0'),
        'slug' => array('min:0'),
        'descricao' => array('min:0'),
        'codigo_barras_1' => array('min:0'),
        'codigo_barras_2' => array('min:0'),
        'codigo_barras_3' => array('min:0'),
        'preco_varejo' => array('min:0'),
        'preco_atacado' => array('min:0'),
        'valor_garcom' => array('min:0'),
        'preco_promocao' => array('min:0'),
        'promocao_inicio' => array('min:0'),
        'promocao_fim' => array('min:0'),
        'trib_icms' => array('min:0'),
        'trib_csosn' => array('min:0'),
        'trib_cst' => array('min:0'),
        'trib_origem_produto' => array('min:0'),
        'trib_cfop_de' => array('min:0'),
        'trib_cfop_fe' => array('min:0'),
        'trib_ncm' => array('min:0'),
        'trib_cest' => array('min:0'),
        'estoque_atual' => array('min:0'),
        'unidade_medida' => array('min:0'),
        'codigo_externo' => array('min:0'),
        'destaque' => array('min:0'),
        'views' => array('min:0'),
        'somente_mesa' => array('min:0'),
        'ordem' => array('min:0'),
    );

    public static $rules_u = array(
        'tenant_id' => array('required','min:1'),
        'titulo' => array('required','min:0'),
        'slug' => array('min:0'),
        'descricao' => array('min:0'),
        'codigo_barras_1' => array('min:0'),
        'codigo_barras_2' => array('min:0'),
        'codigo_barras_3' => array('min:0'),
        'preco_varejo' => array('min:0'),
        'preco_atacado' => array('min:0'),
        'valor_garcom' => array('min:0'),
        'preco_promocao' => array('min:0'),
        'promocao_inicio' => array('min:0'),
        'promocao_fim' => array('min:0'),
        'trib_icms' => array('min:0'),
        'trib_csosn' => array('min:0'),
        'trib_cst' => array('min:0'),
        'trib_origem_produto' => array('min:0'),
        'trib_cfop_de' => array('min:0'),
        'trib_cfop_fe' => array('min:0'),
        'trib_ncm' => array('min:0'),
        'trib_cest' => array('min:0'),
        'estoque_atual' => array('min:0'),
        'unidade_medida' => array('min:0'),
        'codigo_externo' => array('min:0'),
        'destaque' => array('min:0'),
        'views' => array('min:0'),
        'somente_mesa' => array('min:0'),
        'ordem' => array('min:0'),
    );

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tenant_id', 'titulo', 'slug', 'descricao', 'codigo_barras_1', 'codigo_barras_2', 'codigo_barras_3', 'preco_varejo', 'preco_atacado', 'valor_garcom', 'preco_promocao', 'promocao_inicio', 'promocao_fim', 'trib_icms', 'trib_csosn', 'trib_cst', 'trib_origem_produto', 'trib_cfop_de', 'trib_cfop_fe', 'trib_ncm', 'trib_cest', 'estoque_atual', 'unidade_medida', 'codigo_externo', 'destaque', 'deleted_at', 'views', 'somente_mesa', 'ordem'
    ];

    protected $casts = [
        'promocao_inicio' => 'date',
        'promocao_fim' => 'date',
        'preco_varejo' => 'float',
        'preco_atacado' => 'float',
        'valor_garcom' => 'float',
        'preco_promocao' => 'float',
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
        return $this->hasOne('App\Models\Tenant','id','tenant_id');
    }

    public function categorias()
    {
        return $this->belongsToMany('App\Models\Tenant\Categorias','produtos_has_categorias','produtos_id','categorias_id')->withTimestamps();
    }

    public function imagem()
    {
        return $this->hasOne('App\Models\Files', 'rel_id', 'id')->where('files.rel_table', '=', $this->table)->orderBy('files.id', 'asc');
    }

    public function Imagens()
    {
        return $this->hasMany('App\Models\Files', 'rel_id', 'id')->where('files.rel_table', '=', $this->table)->where('files.full_name', '=', '600w')->orderBy('files.id', 'asc');
    }

    public function allimage()
    {
        return $this->hasMany('App\Models\Files', 'rel_id', 'id')->where('files.rel_table', '=', $this->table)->orderBy('files.id', 'asc');
    }

    public function ncm()
    {
        return $this->HasOne('App\Models\Tributacoes\Ncm', 'id', 'trib_ncm');
    }

    public function cest()
    {
        return $this->HasOne('App\Models\Tributacoes\Cest', 'id', 'trib_cest');
    }

    public function cfop_de()
    {
        return $this->HasOne('App\Models\Tributacoes\Cfop', 'id', 'trib_cfop_de');
    }

    public function cfop_fe()
    {
        return $this->HasOne('App\Models\Tributacoes\Cfop', 'id', 'trib_cfop_fe');
    }
}