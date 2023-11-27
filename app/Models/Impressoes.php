<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Impressoes extends Model
{
	protected $table = 'impressoes';
	protected $primaryKey = 'id';
	// public $timestamps = false;
    use SoftDeletes;

	/*Add your validation rules here*/
	public static $rules = array(
        'rel_Table' => array('min:0'),
        'rel_id' => array('min:0'),
        'tipo' => array('min:0'),
        'html' => array('min:0'),
	);

	public static $rules_u = array(
        'rel_Table' => array('min:0'),
        'rel_id' => array('min:0'),
        'tipo' => array('min:0'),
        'html' => array('min:0'),
	);

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'rel_table', 'rel_id', 'tipo', 'html'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'id',
    ];

    public function registros()
    {
        switch ($this->rel_table) {
            case 'vendas':
            return $this->hasOne('App\Model\VendasHead','id','rel_id')->where('rel_table','=','vendas');
                break;
            case 'sangria':
                return $this->hasOne('App\Model\CaixaSangriaEntrada','id','rel_id')->where('rel_table','=','sangrias');
                break;
            default:
                return $this->hasOne('App\Model\VendasHead','id','rel_id')->where('rel_table','=','');
                break;
        }
    }
}