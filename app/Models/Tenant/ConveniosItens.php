<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class ConveniosItens extends Model
{
	protected $table = 'convenios_itens';
	protected $primaryKey = 'id';

	/*Add your validation rules here*/
	public static $rules = array(
        'convenios_head_id' => array('required','min:0'),
		'produtos_id' => array('required','min:1'),
		'descricao' => array('min:0'),
        'quantidade' => array('min:0'),
        'preco' => array('min:0'),
        'desconto' => array('min:0'),
        'valor_total' => array('min:0'),
        'status' => array('min:0'),
        'convenios_recebimentos_id' => array('nullable','min:0'),
	);

	public static $rules_u = array(
        'convenios_head_id' => array('required','min:0'),
        'produtos_id' => array('required','min:1'),
        'descricao' => array('min:0'),
        'quantidade' => array('min:0'),
        'preco' => array('min:0'),
        'desconto' => array('min:0'),
        'valor_total' => array('min:0'),
        'status' => array('min:0'),
        'convenios_recebimentos_id' => array('nullable','min:0'),
	);

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'convenios_head_id', 'produtos_id', 'descricao', 'quantidade', 'preco', 'desconto', 'valor_total', 'status', 'convenios_recebimentos_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'id',
    ];

    public function convenio()
    {
        return $this->hasOne('App\Models\Tenant\ConveniosHead','id','convenios_head_id');
    }

    public function produto()
    {
        return $this->hasOne('App\Models\Tenant\Produtos','id','produtos_id');
    }

    public function recebimento()
    {
        return $this->hasOne('App\Models\Tenant\ConveniosRecebimentos','id','convenios_recebimentos_id');
    }
}