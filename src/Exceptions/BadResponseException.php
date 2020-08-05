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
 * BadResponseException for any errors which cannot be handled nice (ex. 500 etc.)
 */
class BadResponseException extends ApiClientException
{

}
