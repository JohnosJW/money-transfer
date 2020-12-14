<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Transaction
 * @package App\Models
 */
class Transaction extends Model
{
    use SoftDeletes;

    /** @var string  */
    public $table = 'transactions';

    /** @var string  */
    const CREATED_AT = 'created_at';

    /** @var string  */
    const UPDATED_AT = 'updated_at';

    /** @var string  */
    const TYPE_DEBIT = 'debit';

    /** @var string  */
    const TYPE_CREDIT = 'credit';

    /** @var float  */
    const COMMISSION = 1.015;

    /** @var string  */
    const STATUS_PENDING = 'pending';

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
        return $this->belongsTo('App\Models\User');
    }
}
