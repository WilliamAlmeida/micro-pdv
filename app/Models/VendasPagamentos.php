<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class VendasPagamentos extends Model
{
	protected $table = 'vendas_pagamentos';
	protected $primaryKey = 'id';
	public $timestamps = false;

	/*Add your validation rules here*/
	public static $rules = array(
		'caixa_id' => array('required','min:0'),
        'vendas_head_id' => array('required','min:0'),
		'forma_pagamento' => array('required','min:1'),
        'valor' => array('min:0'),
	);

	public static $rules_u = array(
        'caixa_id' => array('required','min:0'),
        'vendas_head_id' => array('required','min:0'),
        'forma_pagamento' => array('required','min:1'),
        'valor' => array('min:0'),
	);

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'caixa_id', 'vendas_head_id', 'forma_pagamento', 'valor'
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
}