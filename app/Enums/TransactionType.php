<?php

declare(strict_types = 1);

namespace App\Enums;


use BenSampo\Enum\Enum;

/**
 * Class TransactionType
 * @package App\Enums
 */
final class TransactionType extends Enum
{
    /** @var string */
    public const TYPE_DEBIT = 'debit';

    /** @var string */
    public const TYPE_CREDIT = 'credit';
}
