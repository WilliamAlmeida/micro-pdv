<?php

namespace App\Models\Tributacoes;

use Illuminate\Database\Eloquent\Model;

class Ncm extends Model
{
	protected $table = 'trib_ncm_tipi';
	protected $primaryKey = 'id';
	public $timestamps = false;

	/*Add your validation rules here*/
	public static $rules = array(
		'ncm' => array('required','min:0'),
		'descricao' => array('required','min:1'),
		'aliq_ipi' => array('min:0'),
	);

	public static $rules_u = array(
		'ncm' => array('required','min:0'),
        'descricao' => array('required','min:1'),
        'aliq_ipi' => array('min:0'),
	);

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'ncm', 'descricao', 'aliq_ipi'
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
        return $this->hasMany('App\Models\Tenant\Produtos','trib_ncm','ncm');
    }
}