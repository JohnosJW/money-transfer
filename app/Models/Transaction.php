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
 * @property string $from_wallet_address
 * @property string $to_wallet_address
 * @property BigInteger $amount
 * @property BigInteger $commission
 * @property string $status
 *
 * Class Transaction
 * @package App\Models
 */
class Transaction extends Model
{
    use SoftDeletes;

    /** @var string  */
    public $table = 'transactions';

    /** @var string  */
    public const TYPE_DEBIT = 'debit';

    /** @var string  */
    public const TYPE_CREDIT = 'credit';

    /** @var string  */
    public const STATUS_DONE = 'done';

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
        'status',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
