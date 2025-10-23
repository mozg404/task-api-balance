<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static Builder<static>|Balance newModelQuery()
 * @method static Builder<static>|Balance newQuery()
 * @method static Builder<static>|Balance query()
 * @mixin Eloquent
 */
class Balance extends Model
{
    protected $fillable = ['user_id', 'amount'];
    protected $casts = ['amount' => 'decimal:2'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
