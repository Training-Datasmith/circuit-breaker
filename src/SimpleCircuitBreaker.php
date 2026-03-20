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

use Presta_Shop\Circuit_Breaker\Contract\Client_Interface;
use Presta_Shop\Circuit_Breaker\Contract\Place_Interface;
use Presta_Shop\Circuit_Breaker\Exception\Unavailable_Service_Exception;
use Presta_Shop\Circuit_Breaker\Storage\Simple_Array;
use Presta_Shop\Circuit_Breaker\System\Main_System;
/**
 * Main implementation of Circuit Breaker.
 */
final class Simple_Circuit_Breaker extends Partial_Circuit_Breaker
{
    public function __construct(Place_Interface $open_place, Place_Interface $half_open_place, Place_Interface $closed_place, Client_Interface $client)
    {
        $system = new Main_System($closed_place, $half_open_place, $open_place);
        parent::__construct($system, $client, new Simple_Array());
    }
    /**
     * {@inheritdoc}
     */
    public function call(string $service, array $service_parameters = [], ?callable $fallback = null): string
    {
        $transaction = $this->init_transaction($service);
        try {
            if ($this->is_opened()) {
                if (!$this->can_access_service($transaction)) {
                    return $this->call_fallback($fallback);
                }
                $this->move_state_to(State::HALF_OPEN_STATE, $service);
            }
            $response = $this->request($service, $service_parameters);
            $this->move_state_to(State::CLOSED_STATE, $service);
            return $response;
        } catch (Unavailable_Service_Exception $exception) {
            $transaction->increment_failures();
            $this->storage->save_transaction($service, $transaction);
            if (!$this->is_allowed_to_retry($transaction)) {
                $this->move_state_to(State::OPEN_STATE, $service);
                return $this->call_fallback($fallback);
            }
            return $this->call($service, $service_parameters, $fallback);
        }
    }
}