<?php

namespace App\Models\Tributacoes;

use Illuminate\Database\Eloquent\Model;

class Cfop extends Model
{
	protected $table = 'trib_cfop';
	protected $primaryKey = 'id';
	public $timestamps = false;

	/*Add your validation rules here*/
	public static $rules = array(
		'cfop' => array('required','min:0'),
		'descricao' => array('required','min:1'),
		'aplicacao' => array('min:0'),
	);

	public static $rules_u = array(
		'cfop' => array('required','min:0'),
        'descricao' => array('required','min:1'),
        'aplicacao' => array('min:0'),
	);

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'cfop', 'descricao', 'aplicacao'
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
        return $this->hasMany('App\Models\Tenant\Produtos','trib_cfop_de','cfop');
    }
}