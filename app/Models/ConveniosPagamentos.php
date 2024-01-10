<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConveniosPagamentos extends Model
{
	protected $table = 'convenios_pagamentos';
	protected $primaryKey = 'id';

	/*Add your validation rules here*/
	public static $rules = array(
        'convenios_recebimentos_id' => array('required','min:0'),
		'forma_pagamento' => array('required','min:1'),
        'valor' => array('min:0'),
	);

	public static $rules_u = array(
        'convenios_recebimentos_id' => array('required','min:0'),
        'forma_pagamento' => array('required','min:1'),
        'valor' => array('min:0'),
	);

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'convenios_recebimentos_id', 'forma_pagamento', 'valor'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'id',
    ];

    public function recebimento()
    {
        return $this->hasOne('App\Models\ConveniosRecebimentos','id','convenios_recebimentos_id');
    }
}