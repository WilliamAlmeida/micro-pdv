<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use App\Traits\HasTenant;
use App\Traits\BelongsToManyTenant;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    // use HasTenant;

    use BelongsToManyTenant;

    /**
    * The model class used for the tenant relationship in the many-to-many association.
    * This can be set using either ::class or the class path as a string.
    *
    * @var string
    */
    // protected $tenant_relation_model = UserTenants::class;

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
        // return $this->hasOne('App\Models\Tenant', 'id', 'empresas_id');
        return $this->tenants();
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
