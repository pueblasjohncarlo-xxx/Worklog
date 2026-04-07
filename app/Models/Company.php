<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use Auditable, HasFactory;

    protected $fillable = [
        'name',
        'industry',
        'type',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'work_opportunities',
        'default_supervisor_id',
        'contact_person',
        'contact_email',
        'contact_phone',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'work_opportunities' => 'array',
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function supervisorProfiles(): HasMany
    {
        return $this->hasMany(SupervisorProfile::class);
    }

    public function mapPin(): HasMany
    {
        return $this->hasMany(MapPin::class);
    }

    /**
     * Get the default supervisor for the company.
     */
    public function defaultSupervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'default_supervisor_id');
    }
}
