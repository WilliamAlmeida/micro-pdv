<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Categorias extends Model
{
    protected $table = 'categorias';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    use SoftDeletes, BelongsToTenant;

    /*Add your validation rules here*/
    public static $rules = array(
        'tenant_id' => array('required','min:1'),
        'titulo' => array('required','min:1','max:100'),
        'slug' => array('min:0','max:150'),
        'ordem' => array('min:0'),
        'file_imagem' => array('min:0'),
        'codigo_barras_1' => array('min:0'),
    );

    public static $rules_u = array(
        'tenant_id' => array('required','min:1'),
        'titulo' => array('required','min:1','max:100'),
        'slug' => array('min:0','max:150'),
        'ordem' => array('min:0'),
        'file_imagem' => array('min:0'),
        'codigo_barras_1' => array('min:0'),
    );

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tenant_id', 'titulo', 'slug', 'ordem', 'file_imagem', 'codigo_barras_1', 'deleted_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'id',
    ];

    const CATEGORIAS = [
        [
            'tenant_id' => null, 'titulo' => 'Geral', 'slug' => 'geral', 'ordem' => 1, 'codigo_barras_1' => null, 'file_imagem' => null
        ]
    ];

    public function empresas()
    {
        return $this->hasOne('App\Models\Tenant','id','tenant_id');
    }

    public function produtos()
    {
        return $this->belongsToMany('App\Models\Tenant\Produtos','produtos_has_categorias','categorias_id','produtos_id')->withTimestamps();
    }

    public function imagem()
    {
        return $this->hasOne('App\Models\Files', 'id', 'file_imagem');
    }
}