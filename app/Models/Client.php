<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_name',
        'vat_number',
        'phone',
        'website',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'currency',
        'customer_group_id',
        'assigned_staff_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the group this customer belongs to.
     */
    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::class);
    }

    /**
     * Get the staff member assigned to this customer.
     */
    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    /**
     * Get the staff member who created this customer.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all customer contacts.
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the primary customer contact.
     */
    public function primaryContact(): HasOne
    {
        return $this->hasOne(Contact::class)->where('is_primary', true);
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'related')->latest();
    }

    /**
     * Limit a query to active customers.
     *
     * @param  Builder<Client>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
