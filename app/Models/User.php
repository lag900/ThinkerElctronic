<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'role',
        'phone',
        'status',
        'avatar',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roleNode()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hasPermission($permission)
    {
        if ($this->isSuperAdmin()) return true;
        if (!$this->roleNode) return false;
        
        return $this->roleNode->permissions()->where('name', $permission)->exists();
    }

    public function isAdmin()
    {
        // Null-safe check for legacy role or new node-based role
        if ($this->role === 'admin') {
            return true;
        }

        return $this->roleNode && $this->roleNode->name === 'super_admin';
    }

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin' || ($this->roleNode && $this->roleNode->name === 'super_admin');
    }

    public function ordersCreated()
    {
        return $this->hasMany(Order::class, 'created_by');
    }
}
