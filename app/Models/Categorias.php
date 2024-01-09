<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categorias extends Model
{
    protected $table = 'categorias';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    use SoftDeletes, HasTenant;

    /*Add your validation rules here*/
    public static $rules = array(
        'empresas_id' => array('required','min:1'),
        'titulo' => array('required','min:1','max:100'),
        'slug' => array('min:0','max:150'),
        'ordem' => array('min:0'),
        'file_imagem' => array('min:0'),
        'codigo_barras_1' => array('min:0'),
    );

    public static $rules_u = array(
        'empresas_id' => array('required','min:1'),
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
        'empresas_id', 'titulo', 'slug', 'ordem', 'file_imagem', 'codigo_barras_1', 'deleted_at'
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
            'empresas_id' => null, 'titulo' => 'Geral', 'slug' => 'geral', 'ordem' => 1, 'codigo_barras_1' => null, 'file_imagem' => null
        ]
    ];

    public function empresas()
    {
        return $this->hasOne('App\Models\Empresas','id','empresas_id');
    }

    public function produtos()
    {
        return $this->belongsToMany('App\Models\Produtos','produtos_has_categorias','categorias_id','produtos_id')->withTimestamps();
    }

    public function imagem()
    {
        return $this->hasOne('App\Models\Files', 'id', 'file_imagem');
    }
}