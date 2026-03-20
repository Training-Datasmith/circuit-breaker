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
namespace Presta_Shop\Circuit_Breaker\Storage;

use Doctrine\Common\Cache\Cache_Provider;
use Presta_Shop\Circuit_Breaker\Contract\Storage_Interface;
use Presta_Shop\Circuit_Breaker\Contract\Transaction_Interface;
use Presta_Shop\Circuit_Breaker\Exception\Transaction_Not_Found_Exception;
/**
 * Implementation of Storage using the Doctrine Cache.
 */
class Doctrine_Cache implements Storage_Interface
{
    /** @var CacheProvider */
    private $cache_provider;
    public function __construct(Cache_Provider $cache_provider)
    {
        $this->cache_provider = $cache_provider;
    }
    /**
     * {@inheritdoc}
     */
    public function save_transaction(string $service, Transaction_Interface $transaction): bool
    {
        $key = $this->get_key($service);
        return $this->cache_provider->save($key, $transaction);
    }
    /**
     * {@inheritdoc}
     */
    public function get_transaction(string $service): Transaction_Interface
    {
        $key = $this->get_key($service);
        if ($this->has_transaction($service)) {
            return $this->cache_provider->fetch($key);
        }
        throw new Transaction_Not_Found_Exception();
    }
    /**
     * {@inheritdoc}
     */
    public function has_transaction(string $service): bool
    {
        $key = $this->get_key($service);
        return $this->cache_provider->contains($key);
    }
    /**
     * {@inheritdoc}
     */
    public function clear(): bool
    {
        return $this->cache_provider->delete_all();
    }
    /**
     * Helper method to properly store the transaction.
     *
     * @param string $service the service URI
     *
     * @return string the transaction unique identifier
     */
    private function get_key(string $service): string
    {
        return md5($service);
    }
}