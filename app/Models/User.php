<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Ramsey\Uuid\Uuid;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    /**
     * Indica se os IDs são autoincrementáveis.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * O tipo de 'casting' da chave primária.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'username', 'password', 'email', 'name', 'phone', 'avatar', 'token', 'salary', 'is_clt', 'is_married', 'has_children',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
        'password',
    ];

    /**
     * O array 'casts' é usado para converter atributos para tipos comuns de dados.
     *
     * @var array
     */
    protected $casts = [
        'is_clt' => 'boolean',
        'is_married' => 'boolean',
        'has_children' => 'boolean',
    ];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
            }
        });
    }
}
