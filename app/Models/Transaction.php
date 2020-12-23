<?php

declare(strict_types = 1);

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $user_id
 * @property string $type
 * @property int $from_wallet_id
 * @property int $to_wallet_id
 * @property int $amount
 * @property int $commission
 *
 * Class Transaction
 * @package App\Models
 */
class Transaction extends Model
{
    use SoftDeletes;

    /** @var array  */
    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    public $fillable = [
        'user_id',
        'type',
        'from_wallet_id',
        'to_wallet_id',
        'amount',
        'commission',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
