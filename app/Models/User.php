<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $timestamps = false; // schema has no created_at/updated_at

    protected $fillable = [
        'full_name', 'email', 'phone', 'password_hash', 'address', 'city',
    ];

    protected $hidden = ['password_hash'];

    // Laravel's auth system expects getAuthPassword() to return the hash.
    // Since your column is password_hash (not "password"), override it:
    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}