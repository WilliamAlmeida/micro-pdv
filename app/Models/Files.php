<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    protected $table = 'files';
    protected $primaryKey = 'id';

    /*Add your validation rules here*/
    public static $rules = array(
      'url' => array('required','min:1'),
      'name' => array('required','min:1'),
      'full_name' => array('required','min:1'),
      'extension' => array('required','min:1'),
      'file_mimetype' => array('min:1'),
      'rel_id' => array('min:0'),
      'rel_table' => array('min:0'),
      );

    public static $rules_u = array(
      'url' => array('required','min:1'),
      'name' => array('required','min:1'),
      'full_name' => array('required','min:1'),
      'extension' => array('required','min:1'),
      'file_mimetype' => array('min:1'),
      'rel_id' => array('min:0'),
      'rel_table' => array('min:0'),
      );

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url', 'name', 'full_name', 'extension', 'file_mimetype', 'rel_id', 'rel_table'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'id',
    ];

    public function logo()
    {
        return $this->hasOne('App\Model\Base\Empresas', 'file_logo', 'id');
    }

    public function background()
    {
        return $this->hasOne('App\Model\Base\Empresas', 'file_background', 'id');
    }

    public function grupo()
    {
        return $this->hasOne('App\Model\Base\Grupos', 'file_imagem', 'id');
    }
}