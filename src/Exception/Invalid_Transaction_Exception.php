<?php

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare (strict_types=1);
namespace Presta_Shop\Circuit_Breaker\Exception;

use Presta_Shop\Circuit_Breaker\Util\Error_Formatter;
final class Invalid_Transaction_Exception extends Circuit_Breaker_Exception
{
    /**
     * @param mixed $service the service URI
     * @param mixed $failures the failures
     * @param mixed $state the Circuit Breaker
     * @param mixed $threshold the threshold
     */
    public static function invalid_parameters($service, $failures, $state, $threshold): self
    {
        $exception_message = 'Invalid parameters for Transaction' . PHP_EOL . Error_Formatter::format('service', $service, 'isURI', 'an URI') . Error_Formatter::format('failures', $failures, 'isPositiveInteger', 'a positive integer') . Error_Formatter::format('state', $state, 'isString', 'a string') . Error_Formatter::format('threshold', $threshold, 'isPositiveInteger', 'a positive integer');
        return new self($exception_message);
    }
}