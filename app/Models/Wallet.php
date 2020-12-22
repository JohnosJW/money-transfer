<?php

declare(strict_types = 1);

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use phpseclib\Math\BigInteger;

/**
 * @property int $user_id
 * @property string $address
 * @property BigInteger $balance
 *
 * Class Wallet
 * @package App\Models
 */
class Wallet extends Model
{
    use SoftDeletes;

    /** @var array  */
    protected $dates = ['deleted_at'];

    /** @var int  */
    public const START_BALANCE = 1;

    /** @var int  */
    public const SATOSHI_IN_ONE_BITCOIN = 100000000;

    /**
     * @var array
     */
    public $fillable = [
        'user_id',
        'balance',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate auto saving fields
     */
    public static function boot(): void
    {
        parent::boot();

        static::creating(function($model)
        {
            $model->address = md5(uniqid('', true));
            $model->balance = self::START_BALANCE * self::SATOSHI_IN_ONE_BITCOIN;
        });
    }
}
