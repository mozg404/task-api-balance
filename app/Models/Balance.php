<?php

namespace App\Models;

use Database\Factories\BalanceFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property numeric $amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @method static BalanceFactory factory($count = null, $state = [])
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
    use HasFactory;

    protected $fillable = ['user_id', 'amount'];
    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function hasEnough(float $amount): bool
    {
        return $this->amount >= $amount;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): BalanceFactory
    {
        return BalanceFactory::new();
    }
}
