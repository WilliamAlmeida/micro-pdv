<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class Caixa extends Model
{
	protected $table = 'caixa';
	protected $primaryKey = 'id';
	public $timestamps = false;

	/*Add your validation rules here*/
	public static $rules = array(
		'user_id' => array('required','min:0'),
        'status' => array('min:0'),
        'valor_inicial' => array('min:0'),
        'sangria_total' => array('min:0'),
        'entrada_total' => array('min:0'),
	);

	public static $rules_u = array(
		'user_id' => array('required','min:0'),
        'status' => array('min:0'),
        'valor_inicial' => array('min:0'),
        'sangria_total' => array('min:0'),
        'entrada_total' => array('min:0'),
	);

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'user_id', 'status', 'valor_inicial', 'sangria_total', 'entrada_total', 'created_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'id',
    ];

    public function user()
    {
        return $this->hasOne('App\Models\User','id','user_id');
    }

    public function vendas()
    {
        return $this->hasMany('App\Models\Tenant\VendasHead','caixa_id','id');
    }

    public function vendas_encerradas()
    {
        return $this->hasMany('App\Models\Tenant\VendasHead','caixa_id','id')->where('status', '=', '1');
    }

    public function vendas_canceladas()
    {
        return $this->hasMany('App\Models\Tenant\VendasHead','caixa_id','id')->where('status', '=', '8');
    }

    public function venda()
    {
        return $this->hasOne('App\Models\Tenant\VendasHead','caixa_id','id')->whereIn('status', [0])->latest();
    }

    public function ultima_venda()
    {
        return $this->hasOne('App\Models\Tenant\VendasHead','caixa_id','id')->whereIn('status', [1])->latest();
    }

    public function itens()
    {
        return $this->hasMany('App\Models\Tenant\VendasItens','caixa_id','id');
    }

    public function pagamentos()
    {
        return $this->hasMany('App\Models\Tenant\VendasPagamentos','caixa_id','id');
    }

    public function sangrias()
    {
        return $this->hasMany('App\Models\Tenant\CaixaSangriaEntrada','caixa_id','id')->where('tipo', '=', 's');
    }

    public function entradas()
    {
        return $this->hasMany('App\Models\Tenant\CaixaSangriaEntrada','caixa_id','id')->where('tipo', '=', 'e');
    }

    public function convenios_recebimentos()
    {
        return $this->hasMany('App\Models\Tenant\ConveniosRecebimentos','caixa_id','id');
    }

    public function convenios_itens()
    {
        return $this->hasManyThrough(
            'App\Models\Tenant\ConveniosItens',
            'App\Models\Tenant\ConveniosHead',
            'caixa_id',                 // Chave estrangeira em convenios_recebimentos que relaciona ao Caixa
            'convenios_head_id',        // Chave estrangeira em convenios_itens que relaciona ao ConveniosHead
            'id',                       // Chave primária na tabela Caixa
            'id'                        // Chave primária em convenios_head
        );
    }

    public function convenios_itens_pendentes()
    {
        return $this->hasManyThrough(
            'App\Models\Tenant\ConveniosItens',
            'App\Models\Tenant\ConveniosHead',
            'caixa_id',                 // Chave estrangeira em convenios_recebimentos que relaciona ao Caixa
            'convenios_head_id',        // Chave estrangeira em convenios_itens que relaciona ao ConveniosHead
            'id',                       // Chave primária na tabela Caixa
            'id'                        // Chave primária em convenios_head
        )->where('status', 0);
    }

    public function convenios_itens_cancelados()
    {
        return $this->hasManyThrough(
            'App\Models\Tenant\ConveniosItens',
            'App\Models\Tenant\ConveniosHead',
            'caixa_id',                 // Chave estrangeira em convenios_recebimentos que relaciona ao Caixa
            'convenios_head_id',        // Chave estrangeira em convenios_itens que relaciona ao ConveniosHead
            'id',                       // Chave primária na tabela Caixa
            'id'                        // Chave primária em convenios_head
        )->where('status', 2);
    }

    public function convenios_itens_pagos()
    {
        return $this->hasManyThrough(
            'App\Models\Tenant\ConveniosItens',
            'App\Models\Tenant\ConveniosRecebimentos',
            'caixa_id',                 // Chave estrangeira em convenios_recebimentos que relaciona ao Caixa
            'convenios_recebimentos_id',// Chave estrangeira em convenios_itens que relaciona ao ConveniosRecebimentos
            'id',                       // Chave primária na tabela Caixa
            'id'                        // Chave primária em convenios_recebimentos
        )->where('status', 1);
    }

    public function validDataAbertura(): bool
    {
        $open = \Carbon\Carbon::parse($this->created_at);

        return $open->diffInDays(\Carbon\Carbon::now()) >= 2;
    }
}