<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tenant extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'id_card',
        'address_origin',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
