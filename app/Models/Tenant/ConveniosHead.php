<?php

namespace App\Models\Tenant;

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
        return $this->hasOne('App\Models\Tenant\Caixa','id','caixa_id');
    }

    public function cliente()
    {
        return $this->hasOne('App\Models\Tenant\Clientes','id','clientes_id');
    }

    public function venda()
    {
        return $this->hasOne('App\Models\Tenant\VendasHead','id','vendas_head_id');
    }

    public function itens()
    {
        return $this->hasMany('App\Models\Tenant\ConveniosItens','convenios_head_id','id');
    }
}