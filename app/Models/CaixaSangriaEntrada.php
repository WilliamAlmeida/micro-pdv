<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CaixaSangriaEntrada extends Model
{
	protected $table = 'caixa_sangria_entrada';
	protected $primaryKey = 'id';
	public $timestamps = false;

	/*Add your validation rules here*/
	public static $rules = array(
		'caixa_id' => array('required','min:0'),
        'tipo' => array('min:0'),
		'motivo' => array('min:0'),
        'valor' => array('min:0'),
	);

	public static $rules_u = array(
		'caixa_id' => array('required','min:0'),
        'tipo' => array('min:0'),
        'motivo' => array('min:0'),
        'valor' => array('min:0'),
	);

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'caixa_id', 'tipo', 'motivo', 'valor'
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

    public function impressoes()
    {
        return $this->hasMany('App\Model\Impressoes','rel_id','id')->where('rel_table','=','sangrias');
    }
}