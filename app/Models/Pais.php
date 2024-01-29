<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
	protected $table = 'paises';
	protected $primaryKey = 'id';
	public $timestamps = false;

	/*Add your validation rules here*/
	public static $rules = array(
		'nome' => array('unique:cidade','required','min:1','alpha'),
		'codigo' => array('required','min:1','numeric'),
		'sigla' => array('required','min:2','max:2','alpha'),
	);

	public static $rules_u = array(
		'nome' => array('required','min:1','alpha'),
		'codigo' => array('required','min:1','numeric'),
		'sigla' => array('required','min:2','max:2','alpha'),
	);

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'nome', 'codigo', 'sigla'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'id',
    ];

    public function estados()
    {
    	return $this->hasMany('App\Models\Estado', 'pais_id', 'id');
    }
}