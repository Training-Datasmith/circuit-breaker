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
namespace Presta_Shop\Circuit_Breaker\Client;

use Exception;
use Guzzle_Http\Client as OriginalGuzzleClient;
use Presta_Shop\Circuit_Breaker\Contract\Client_Interface;
use Presta_Shop\Circuit_Breaker\Exception\Unavailable_Service_Exception;
use Presta_Shop\Circuit_Breaker\Exception\Unsupported_Method_Exception;
/**
 * Guzzle implementation of client.
 * The possibility of extending this client is intended.
 */
class Guzzle_Client implements Client_Interface
{
    /**
     * @var string by default, calls are sent using GET method
     */
    public const DEFAULT_METHOD = 'GET';
    /**
     * Supported HTTP methods
     */
    public const SUPPORTED_METHODS = ['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'OPTIONS'];
    /**
     * @var array the Client default options
     */
    private $default_options;
    public function __construct(array $default_options = [])
    {
        $this->default_options = $default_options;
    }
    /**
     * {@inheritdoc}
     *
     * @throws UnavailableServiceException
     */
    public function request(string $resource, array $options): string
    {
        try {
            $options = array_merge($this->default_options, $options);
            $client = new Original_Guzzle_Client($options);
            $method = $this->get_http_method($options);
            $options['exceptions'] = true;
            return (string) $client->request($method, $resource, $options)->get_body();
        } catch (Exception $e) {
            throw new Unavailable_Service_Exception($e->get_message(), (int) $e->get_code(), $e);
        }
    }
    /**
     * @param array $options the list of options
     *
     * @return string the method
     *
     * @throws UnsupportedMethodException
     */
    private function get_http_method(array $options): string
    {
        if (isset($options['method'])) {
            if (!in_array($options['method'], self::SUPPORTED_METHODS)) {
                throw Unsupported_Method_Exception::unsupported_method($options['method']);
            }
            return $options['method'];
        }
        return self::DEFAULT_METHOD;
    }
}