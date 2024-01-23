<?php

namespace App\Models\Tributacoes;

use Illuminate\Database\Eloquent\Model;

class Cest extends Model
{
	protected $table = 'trib_cest';
	protected $primaryKey = 'id';
	public $timestamps = false;

	/*Add your validation rules here*/
	public static $rules = array(
		'cest' => array('required','min:0'),
		'descricao' => array('required','min:1'),
		'ncm_id' => array('min:0'),
	);

	public static $rules_u = array(
		'cest' => array('required','min:0'),
        'descricao' => array('required','min:1'),
        'ncm_id' => array('min:0'),
	);

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'cest', 'descricao', 'ncm_id'
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
        return $this->hasMany('App\Models\Tenant\Produtos','trib_cest','cest');
    }

    public function ncm()
    {
        return $this->hasOne('App\Models\Tributacoes\Ncm','id','ncm_id');
    }
}