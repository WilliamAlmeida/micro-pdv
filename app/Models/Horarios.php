<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Horarios extends Model
{
	protected $table = 'horarios';
	protected $primaryKey = 'id';
	// public $timestamps = false;

    use BelongsToTenant;

	/*Add your validation rules here*/
	public static $rules = array(
        'tenant_id' => array('required','min:1'),
		'dia' => array('required','min:1','max:45'),
        'inicio' => array('min:0','time'),
        'fim' => array('min:0','time'),
        'fim' => array('min:0','time'),
	);

	public static $rules_u = array(
        'tenant_id' => array('required','min:1'),
        'dia' => array('required','min:1','max:45'),
        'inicio' => array('min:0','time'),
        'fim' => array('min:0','time'),
	);

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'tenant_id', 'dia', 'inicio', 'fim'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'id',
    ];

    public function empresas()
    {
        return $this->hasOne('App\Models\Tenant','id','tenant_id');
    }
}