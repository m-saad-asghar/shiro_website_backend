<?php

namespace App\Models;

class Team extends BaseModel
{
    protected $fillable = [
        'name',
        'team_type',
        'position',
        'bio',
        'image',
        'facebook',
        'linkedin',
        'instagram',
        'sort',
        'phone',
        'whatsapp',
        'experience',
        'languages',
        'areas_of_expertise',
        'developers_of_expertise',
    ];

    protected $casts = [
        'languages' => 'array',
        'sort' => 'integer',
    ];

    protected $translatable = [
        'name',
        'position',
        'bio',
        'areas_of_expertise',
    ];

    // Constants for team types
    const TYPE_MANAGEMENT = 'management';
    const TYPE_BROKERS = 'brokers';

    // Get all team types
    public static function getTeamTypes()
    {
        return [
            self::TYPE_MANAGEMENT => 'Management',
            self::TYPE_BROKERS => 'Brokers',
        ];
    }

    // Scopes for filtering by team type
    public function scopeManagement($query)
    {
        return $query->where('team_type', self::TYPE_MANAGEMENT);
    }

    public function scopeBrokers($query)
    {
        return $query->where('team_type', self::TYPE_BROKERS);
    }

    // Get formatted languages
    public function getFormattedLanguagesAttribute()
    {
        if (!$this->languages || !is_array($this->languages)) {
            return 'Not specified';
        }
        
        return implode(', ', $this->languages);
    }

    // Check if member has social media links
    public function hasSocialMediaAttribute()
    {
        return !empty($this->facebook) || !empty($this->linkedin) || !empty($this->instagram);
    }

    // Get social media links array
    public function getSocialLinksAttribute()
    {
        $links = [];
        
        if ($this->facebook) {
            $links['facebook'] = $this->facebook;
        }
        
        if ($this->linkedin) {
            $links['linkedin'] = $this->linkedin;
        }
        
        if ($this->instagram) {
            $links['instagram'] = $this->instagram;
        }
        
        return $links;
    }
}
