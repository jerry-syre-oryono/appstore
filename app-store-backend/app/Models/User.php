<?php
namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden = ['password'];

    public function isAdmin() { return $this->role === 'admin'; }
    public function submissions() { return $this->hasMany(Submission::class); }
    public function pushTokens() { return $this->hasMany(PushToken::class); }
}
