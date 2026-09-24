<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'last_name', 'first_name', 'middle_name', 'birthday', 'city',
        'workplace', 'position', 'email', 'phone', 'source', 'ip', 'consent_at',
    ];

    protected $casts = [
        'birthday' => 'date',
        'consent_at' => 'datetime',
    ];

    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([$this->last_name, $this->first_name, $this->middle_name])));
    }
}
