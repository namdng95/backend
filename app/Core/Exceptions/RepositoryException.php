<?php

namespace App\Core\Exceptions;

use App\Core\Exceptions\BaseException;

/**
 * @method static \Throwable invalidMethod()
 * @method static \Throwable invalidModel()
 */
class RepositoryException extends BaseException
{
    /**
     * Get prefix of code
     *
     * @return string
     */
    protected static function getPrefix()
    {
        return 'repository';
    }
}
