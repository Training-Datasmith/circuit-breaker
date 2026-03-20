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
namespace Presta_Shop\Circuit_Breaker\Util;

/**
 * Helper to provide complete and easy to read
 * error messages.
 * Mostly used to build Exception messages.
 */
final class Error_Formatter
{
    /**
     * Format error message.
     *
     * @param string $parameter the parameter to evaluate
     * @param mixed $value the value to format
     * @param string $function the validation function
     * @param string $expectedType the expected type
     */
    public static function format(string $parameter, $value, string $function, string $expected_type): string
    {
        $error_message = '';
        $is_valid = Assert::$function($value);
        $type = gettype($value);
        $has_string_value = in_array($type, ['integer', 'float', 'string'], true);
        if (!$is_valid) {
            $error_message = sprintf('Excepted %s to be %s, got %s', $parameter, $expected_type, $type);
            if ($has_string_value) {
                $error_message .= sprintf(' (%s)', (string) $value);
            }
            $error_message .= PHP_EOL;
        }
        return $error_message;
    }
}