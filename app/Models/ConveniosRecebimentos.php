<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConveniosRecebimentos extends Model
{
	protected $table = 'convenios_recebimentos';
	protected $primaryKey = 'id';

	/*Add your validation rules here*/
	public static $rules = array(
        'convenios_head_id' => array('required','min:0'),
        'desconto' => array('min:0'),
        'troco' => array('min:0'),
        'valor_total' => array('min:0'),
	);

	public static $rules_u = array(
        'convenios_head_id' => array('required','min:0'),
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
    	'convenios_head_id', 'desconto', 'troco', 'valor_total'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'id',
    ];

    public function itens()
    {
        return $this->hasMany('App\Models\ConveniosItens','convenios_recebimentos_id','id');
    }

    public function pagamentos()
    {
        return $this->hasMany('App\Models\ConveniosPagamentos','convenios_recebimentos_id','id');
    }
}