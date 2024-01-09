<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
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

    static public function listTypeUser(): array
    {
        $USER  = 0;
        $ADMIN = 1;

        return [
            ['type' => $USER, 'label' => 'Usuário'],
            ['type' => $ADMIN, 'label' => 'Admin'],
        ];
    }

    public function getTypeUser(): string
    {
        $USER  = 0;
        $ADMIN = 1;

        return match ($this->is_admin) {
            $USER  => "Usuário",
            $ADMIN => "Admin",
        };
    }

    public function empresa()
    {
        return $this->hasOne('App\Models\Empresas', 'id', 'empresas_id');
    }

    public function caixas()
    {
        return $this->hasMany('App\Models\Caixa', 'user_id', 'id');
    }

    public function caixa()
    {
        return $this->hasOne('App\Models\Caixa', 'user_id', 'id')->whereIn('status', [0])->latest();
    }
}
