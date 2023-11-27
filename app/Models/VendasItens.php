<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class VendasItens extends Model
{
	protected $table = 'vendas_itens';
	protected $primaryKey = 'id';
	public $timestamps = false;

	/*Add your validation rules here*/
	public static $rules = array(
		'caixa_id' => array('required','min:0'),
        'vendas_head_id' => array('required','min:0'),
		'produtos_id' => array('required','min:1'),
		'descricao' => array('min:0'),
        'quantidade' => array('min:0'),
        'preco' => array('min:0'),
        'desconto' => array('min:0'),
        'valor_total' => array('min:0'),
	);

	public static $rules_u = array(
		'caixa_id' => array('required','min:0'),
        'vendas_head_id' => array('required','min:0'),
        'produtos_id' => array('required','min:1'),
        'descricao' => array('min:0'),
        'quantidade' => array('min:0'),
        'preco' => array('min:0'),
        'desconto' => array('min:0'),
        'valor_total' => array('min:0'),
	);

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'caixa_id', 'vendas_head_id', 'produtos_id', 'descricao', 'quantidade', 'preco', 'desconto', 'valor_total'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'id',
    ];

    public function caixa()
    {
        return $this->hasOne('App\Model\Caixa','id','caixa_id');
    }

    public function venda()
    {
        return $this->hasOne('App\Model\VendasHead','id','vendas_head_id');
    }

    public function produtos()
    {
        return $this->hasOne('App\Model\Produtos','id','produtos_id');
    }
}