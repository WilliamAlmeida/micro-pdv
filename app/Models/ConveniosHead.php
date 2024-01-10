<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConveniosHead extends Model
{
	protected $table = 'convenios_head';
	protected $primaryKey = 'id';

	/*Add your validation rules here*/
	public static $rules = array(
		'caixa_id' => array('required','min:0'),
		'vendas_head_id' => array('required','min:0'),
        'clientes_id' => array('required','min:0'),
	);

	public static $rules_u = array(
		'caixa_id' => array('required','min:0'),
		'vendas_head_id' => array('required','min:0'),
        'clientes_id' => array('required','min:0'),
	);

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'caixa_id', 'vendas_head_id', 'clientes_id'
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
        return $this->hasOne('App\Models\Caixa','id','caixa_id');
    }

    public function cliente()
    {
        return $this->hasOne('App\Models\Clientes','id','clientes_id');
    }

    public function venda()
    {
        return $this->hasOne('App\Models\VendasHead','id','vendas_head_id');
    }

    public function recebimentos()
    {
        return $this->hasMany('App\Models\ConveniosRecebimentos','convenios_head_id','id');
    }

    public function itens()
    {
        return $this->hasMany('App\Models\ConveniosItens','convenios_head_id','id');
    }
}