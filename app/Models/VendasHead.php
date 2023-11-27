<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class VendasHead extends Model
{
	protected $table = 'vendas_head';
	protected $primaryKey = 'id';
	public $timestamps = false;

	/*Add your validation rules here*/
	public static $rules = array(
		'caixa_id' => array('required','min:0'),
		'status' => array('min:0'),
        'desconto' => array('min:0'),
        'troco' => array('min:0'),
        'valor_total' => array('min:0'),
	);

	public static $rules_u = array(
		'caixa_id' => array('required','min:0'),
        'status' => array('min:0'),
        'desconto' => array('min:0'),
        'troco' => array('min:0'),
        'valor_total' => array('min:0'),
	);

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'caixa_id', 'status', 'desconto', 'troco', 'valor_total'
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

    public function itens()
    {
        return $this->hasMany('App\Model\VendasItens','vendas_head_id','id');
    }

    public function pagamentos()
    {
        return $this->hasMany('App\Model\VendasPagamentos','vendas_head_id','id');
    }

    public function impressoes()
    {
        return $this->hasMany('App\Model\Impressoes','rel_id','id')->where('rel_table','=','vendas');
    }
}