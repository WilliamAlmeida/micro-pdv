<?php

namespace App\Models;

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
    	'user_id', 'status', 'valor_inicial', 'sangria_total', 'entrada_total'
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
        return $this->hasMany('App\Models\VendasHead','caixa_id','id');
    }

    public function vendas_encerradas()
    {
        return $this->hasMany('App\Models\VendasHead','caixa_id','id')->where('status', '=', '1');
    }

    public function vendas_canceladas()
    {
        return $this->hasMany('App\Models\VendasHead','caixa_id','id')->where('status', '=', '8');
    }

    public function venda()
    {
        return $this->hasOne('App\Models\VendasHead','caixa_id','id')->whereIn('status', [0])->latest();
    }

    public function ultima_venda()
    {
        return $this->hasOne('App\Models\VendasHead','caixa_id','id')->whereIn('status', [1])->latest();
    }

    public function itens()
    {
        return $this->hasMany('App\Models\VendasItens','caixa_id','id');
    }

    public function pagamentos()
    {
        return $this->hasMany('App\Models\VendasPagamentos','caixa_id','id');
    }

    public function sangrias()
    {
        return $this->hasMany('App\Models\CaixaSangriaEntrada','caixa_id','id')->where('tipo', '=', 's');
    }

    public function entradas()
    {
        return $this->hasMany('App\Models\CaixaSangriaEntrada','caixa_id','id')->where('tipo', '=', 'e');
    }

    public function convenios_recebimentos()
    {
        return $this->hasMany('App\Models\ConveniosRecebimentos','caixa_id','id');
    }

    public function convenios_itens()
    {
        return $this->hasManyThrough(
            'App\Models\ConveniosItens',
            'App\Models\ConveniosHead',
            'caixa_id',                 // Chave estrangeira em convenios_recebimentos que relaciona ao Caixa
            'convenios_head_id',        // Chave estrangeira em convenios_itens que relaciona ao ConveniosHead
            'id',                       // Chave primária na tabela Caixa
            'id'                        // Chave primária em convenios_head
        );
    }

    public function convenios_itens_pendentes()
    {
        return $this->hasManyThrough(
            'App\Models\ConveniosItens',
            'App\Models\ConveniosHead',
            'caixa_id',                 // Chave estrangeira em convenios_recebimentos que relaciona ao Caixa
            'convenios_head_id',        // Chave estrangeira em convenios_itens que relaciona ao ConveniosHead
            'id',                       // Chave primária na tabela Caixa
            'id'                        // Chave primária em convenios_head
        )->where('status', 0);
    }

    public function convenios_itens_cancelados()
    {
        return $this->hasManyThrough(
            'App\Models\ConveniosItens',
            'App\Models\ConveniosHead',
            'caixa_id',                 // Chave estrangeira em convenios_recebimentos que relaciona ao Caixa
            'convenios_head_id',        // Chave estrangeira em convenios_itens que relaciona ao ConveniosHead
            'id',                       // Chave primária na tabela Caixa
            'id'                        // Chave primária em convenios_head
        )->where('status', 2);
    }

    public function convenios_itens_pagos()
    {
        return $this->hasManyThrough(
            'App\Models\ConveniosItens',
            'App\Models\ConveniosRecebimentos',
            'caixa_id',                 // Chave estrangeira em convenios_recebimentos que relaciona ao Caixa
            'convenios_recebimentos_id',// Chave estrangeira em convenios_itens que relaciona ao ConveniosRecebimentos
            'id',                       // Chave primária na tabela Caixa
            'id'                        // Chave primária em convenios_recebimentos
        )->where('status', 1);
    }

    public function validDataAbertura(): bool
    {
        return \Carbon\Carbon::parse($this->created_at)->format('Y-m-d') <= now()->subDays(3)->format('Y-m-d');
    }
}