<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;

class Admin extends BaseAuthModel implements FilamentUser
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Determine if the user can access the Filament admin panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // السماح لجميع المسؤولين بالدخول
        return true;
        
        // أو إذا كنت تريد فحص البريد الإلكتروني:
        // return str_ends_with($this->email, '@shiroestate.ae') && $this->hasVerifiedEmail();
    }
    
    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName(): string
    {
        return 'id';  // استخدام ID بدلاً من email
    }
}
