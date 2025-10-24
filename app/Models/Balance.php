<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property numeric $amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static Builder<static>|Balance newModelQuery()
 * @method static Builder<static>|Balance newQuery()
 * @method static Builder<static>|Balance query()
 * @method static Builder<static>|Balance whereAmount($value)
 * @method static Builder<static>|Balance whereCreatedAt($value)
 * @method static Builder<static>|Balance whereId($value)
 * @method static Builder<static>|Balance whereUpdatedAt($value)
 * @method static Builder<static>|Balance whereUserId($value)
 * @mixin Eloquent
 */
class Balance extends Model
{
    protected $fillable = ['user_id', 'amount'];
    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
