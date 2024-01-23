<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasTenant;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'type',
        'empresas_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected const USER = 0;
    protected const EMPRESA = 1;
    protected const ADMIN = 2;

    static public $list_type_user = [
        ['type' => self::USER, 'label' => 'Usuário'],
        ['type' => self::EMPRESA, 'label' => 'Empresa'],
        ['type' => self::ADMIN, 'label' => 'Admin'],
    ];

    public function getTypeUser(): string
    {
        return match ($this->type) {
            self::USER  => "Usuário",
            self::EMPRESA => "Empresa",
            self::ADMIN => "Admin"
        };
    }

    public function isAdmin(): bool
    {
        return $this->type == self::ADMIN;
    }

    public function isEmpresa(): bool
    {
        return $this->type == self::EMPRESA;
    }

    public function isUser(): bool
    {
        return $this->type == self::USER;
    }

    public function empresa()
    {
        return $this->hasOne('App\Models\Empresas', 'id', 'empresas_id');
    }

    public function caixas()
    {
        return $this->hasMany('App\Models\Tenant\Caixa', 'user_id', 'id');
    }

    public function caixa()
    {
        return $this->hasOne('App\Models\Tenant\Caixa', 'user_id', 'id')->whereIn('status', [0])->latest();
    }
}
