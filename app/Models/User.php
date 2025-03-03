<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
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
    
    /**
     * The role hierarchy for permission checks.
     * Higher value means higher permissions.
     */
    protected $roleHierarchy = [
        'user' => 1,
        'nurse' => 2,
        'doctor' => 3,
        'admin' => 4,
        'superadmin' => 5, // Highest level
    ];
    
    /**
     * Check if the user has a specific role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }
    
    /**
     * Check if the user has one of the specified roles.
     *
     * @param array $roles
     * @return bool
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }
    
    /**
     * Check if the user has at least the specified role level.
     *
     * @param string $role
     * @return bool
     */
    public function hasRoleLevel(string $role): bool
    {
        // Superadmin has access to everything
        if ($this->role === 'superadmin') {
            return true;
        }
        
        $requiredLevel = $this->roleHierarchy[$role] ?? 0;
        $userLevel = $this->roleHierarchy[$this->role] ?? 0;
        
        return $userLevel >= $requiredLevel;
    }
    
    /**
     * Check if the user is a superadmin.
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }
}
