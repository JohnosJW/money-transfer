<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Wallet
 * @package App\Models
 */
class Wallet extends Model
{
    use SoftDeletes;

    /** @var string  */
    public $table = 'wallets';

    /** @var string  */
    const CREATED_AT = 'created_at';

    /** @var string  */
    const UPDATED_AT = 'updated_at';

    /** @var array  */
    protected $dates = ['deleted_at'];

    /** @var int  */
    const START_BALANCE = 100;

    /** @var int  */
    const CENTS_IN_ONE_CURRENCY = 100;

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
        return $this->belongsTo('App\Models\User');
    }

    /**
     * Generate auto saving fields
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function($model)
        {
            $model->address = md5(uniqid());
            $model->balance = self::START_BALANCE * self::CENTS_IN_ONE_CURRENCY;
        });
    }
}
