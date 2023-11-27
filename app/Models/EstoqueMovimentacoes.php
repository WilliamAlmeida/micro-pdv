<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class EstoqueMovimentacoes extends Model
{
    protected $table = 'estoque_movimentacoes';
    protected $primaryKey = 'id';
    // public $timestamps = false;

    /*Add your validation rules here*/
    public static $rules = array(
        'produtos_id' => array('required','min:1'),
        'tipo' => array('required','min:0'),
        'quantidade' => array('min:0'),
        'motivo' => array('min:0'),
        'fornecedores_id' => array('min:0'),
        'nota_fiscal' => array('min:0'),
    );

    public static $rules_u = array(
        'produtos_id' => array('required','min:1'),
        'tipo' => array('required','min:0'),
        'quantidade' => array('min:0'),
        'motivo' => array('min:0'),
        'fornecedores_id' => array('min:0'),
        'nota_fiscal' => array('min:0'),
    );

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'produtos_id', 'tipo', 'quantidade', 'motivo', 'fornecedores_id', 'nota_fiscal'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'id',
    ];

    public function produtos()
    {
        return $this->hasOne('App\Model\Produtos','id','produtos_id');
    }

    public function fornecedores()
    {
        return $this->hasOne('App\Model\Fornecedores','id','fornecedores_id');
    }
}