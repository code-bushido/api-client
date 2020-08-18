<?php
/*
 * This file is part of the Bushido\ApiClient package.
 *
 * (c) Wojciech Nowicki <wnowicki@me.com>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Bushido\ApiClient\Exceptions;

/**
 * ErrorResponseException for Error responses that can be handled in a nice way
 * (part of API definition, ex. 404 not found).
 */
class ErrorResponseException extends ApiClientException
{

}
