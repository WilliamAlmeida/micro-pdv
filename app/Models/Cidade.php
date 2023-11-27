<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Cidade extends Model
{
	protected $table = 'cidade';
	protected $primaryKey = 'id';
	public $timestamps = false;

	/*Add your validation rules here*/
	public static $rules = array(
		'nome' => array('unique:cidade','required','min:1','alpha'),
		'codigo' => array('required','min:1','numeric'),
		'estado_id' => array('required','min:1','numeric'),
	);

	public static $rules_u = array(
		'nome' => array('required','min:1','alpha'),
		'codigo' => array('required','min:1','numeric'),
		'estado_id' => array('required','min:1','numeric'),
	);

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'nome', 'codigo', 'estado_id', 
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'id',
    ];

    public function estado()
    {
    	return $this->hasOne('App\Model\Estado', 'id', 'estado_id');
    }

    public function user()
    {
    	return $this->hasMany('App\User', 'end_idcidade', 'id');
    }
}