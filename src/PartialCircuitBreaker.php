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
namespace Presta_Shop\Circuit_Breaker;

use DateTime;
use Presta_Shop\Circuit_Breaker\Client\Guzzle_Client;
use Presta_Shop\Circuit_Breaker\Contract\Circuit_Breaker_Interface;
use Presta_Shop\Circuit_Breaker\Contract\Client_Interface;
use Presta_Shop\Circuit_Breaker\Contract\Place_Interface;
use Presta_Shop\Circuit_Breaker\Contract\Storage_Interface;
use Presta_Shop\Circuit_Breaker\Contract\System_Interface;
use Presta_Shop\Circuit_Breaker\Contract\Transaction_Interface;
use Presta_Shop\Circuit_Breaker\Transaction\Simple_Transaction;
abstract class Partial_Circuit_Breaker implements Circuit_Breaker_Interface
{
    public function __construct(System_Interface $system, Client_Interface $client, Storage_Interface $storage)
    {
        $this->current_place = $system->get_initial_place();
        $this->places = $system->get_places();
        $this->client = $client;
        $this->storage = $storage;
    }
    /**
     * @var ClientInterface the Client that consumes the service URI
     */
    protected $client;
    /**
     * @var PlaceInterface the current Place of the Circuit Breaker
     */
    protected $current_place;
    /**
     * @var PlaceInterface[] the Circuit Breaker places
     */
    protected $places = [];
    /**
     * @var StorageInterface the Circuit Breaker storage
     */
    protected $storage;
    /**
     * {@inheritdoc}
     */
    abstract public function call(string $service, array $service_parameters = [], ?callable $fallback = null): string;
    /**
     * {@inheritdoc}
     */
    public function get_state(): string
    {
        return $this->current_place->get_state();
    }
    /**
     * {@inheritdoc}
     */
    public function is_opened(): bool
    {
        return State::OPEN_STATE === $this->current_place->get_state();
    }
    /**
     * {@inheritdoc}
     */
    public function is_half_opened(): bool
    {
        return State::HALF_OPEN_STATE === $this->current_place->get_state();
    }
    /**
     * {@inheritdoc}
     */
    public function is_closed(): bool
    {
        return State::CLOSED_STATE === $this->current_place->get_state();
    }
    protected function call_fallback(?callable $fallback = null): string
    {
        if (null === $fallback) {
            return '';
        }
        return (string) call_user_func($fallback);
    }
    /**
     * @param string $state the Place state
     * @param string $service the service URI
     */
    protected function move_state_to(string $state, string $service): bool
    {
        $this->current_place = $this->places[$state];
        $transaction = Simple_Transaction::create_from_place($this->current_place, $service);
        return $this->storage->save_transaction($service, $transaction);
    }
    /**
     * @param string $service the service URI
     */
    protected function init_transaction(string $service): Transaction_Interface
    {
        if ($this->storage->has_transaction($service)) {
            $transaction = $this->storage->get_transaction($service);
            // CircuitBreaker needs to be in the same state as its last transaction
            if ($this->get_state() !== $transaction->get_state()) {
                $this->current_place = $this->places[$transaction->get_state()];
            }
        } else {
            $transaction = Simple_Transaction::create_from_place($this->current_place, $service);
            $this->storage->save_transaction($service, $transaction);
        }
        return $transaction;
    }
    /**
     * @param TransactionInterface $transaction the Transaction
     */
    protected function is_allowed_to_retry(Transaction_Interface $transaction): bool
    {
        return $transaction->get_failures() < $this->current_place->get_failures();
    }
    /**
     * @param TransactionInterface $transaction the Transaction
     */
    protected function can_access_service(Transaction_Interface $transaction): bool
    {
        return $transaction->get_threshold_date_time() < new DateTime();
    }
    /**
     * Calls the client with the right information.
     *
     * @param string $service the service URI
     * @param array $parameters the service URI parameters
     */
    protected function request(string $service, array $parameters = []): string
    {
        $forced_parameters = ['timeout' => $this->current_place->get_timeout()];
        if ($this->client instanceof Guzzle_Client) {
            $forced_parameters['connect_timeout'] = $this->current_place->get_timeout();
        }
        return $this->client->request($service, array_merge($parameters, $forced_parameters));
    }
}