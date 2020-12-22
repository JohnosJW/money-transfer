<?php

declare(strict_types = 1);

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use phpseclib\Math\BigInteger;

/**
 * @property int $user_id
 * @property string $type
 * @property int $from_wallet_id
 * @property int $to_wallet_id
 * @property BigInteger $amount
 * @property BigInteger $commission
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
        'from_wallet_address',
        'to_wallet_address',
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
