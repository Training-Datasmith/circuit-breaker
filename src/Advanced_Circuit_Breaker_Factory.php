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

use Presta_Shop\Circuit_Breaker\Client\Symfony_Http_Client;
use Presta_Shop\Circuit_Breaker\Contract\Circuit_Breaker_Interface;
use Presta_Shop\Circuit_Breaker\Contract\Client_Interface;
use Presta_Shop\Circuit_Breaker\Contract\Factory_Interface;
use Presta_Shop\Circuit_Breaker\Contract\Factory_Settings_Interface;
use Presta_Shop\Circuit_Breaker\Contract\Storage_Interface;
use Presta_Shop\Circuit_Breaker\Contract\Transition_Dispatcher_Interface;
use Presta_Shop\Circuit_Breaker\Place\Closed_Place;
use Presta_Shop\Circuit_Breaker\Place\Half_Open_Place;
use Presta_Shop\Circuit_Breaker\Place\Open_Place;
use Presta_Shop\Circuit_Breaker\Storage\Simple_Array;
use Presta_Shop\Circuit_Breaker\System\Main_System;
use Presta_Shop\Circuit_Breaker\Transition\Null_Dispatcher;
/**
 * Advanced implementation of Circuit Breaker Factory
 * Used to create an AdvancedCircuitBreaker instance.
 */
final class Advanced_Circuit_Breaker_Factory implements Factory_Interface
{
    /**
     * {@inheritdoc}
     */
    public function create(Factory_Settings_Interface $settings): Circuit_Breaker_Interface
    {
        $closed_place = new Closed_Place($settings->get_failures(), $settings->get_timeout(), 0);
        $open_place = new Open_Place(0, 0, $settings->get_threshold());
        $half_open_place = new Half_Open_Place($settings->get_failures(), $settings->get_stripped_timeout(), 0);
        $system = new Main_System($closed_place, $half_open_place, $open_place);
        /** @var ClientInterface $client */
        $client = $settings->get_client() ?: new Symfony_Http_Client($settings->get_client_options());
        /** @var StorageInterface $storage */
        $storage = $settings->get_storage() ?: new Simple_Array();
        /** @var TransitionDispatcherInterface $dispatcher */
        $dispatcher = $settings->get_dispatcher() ?: new Null_Dispatcher();
        $circuit_breaker = new Advanced_Circuit_Breaker($system, $client, $storage, $dispatcher);
        if (null !== $settings->get_default_fallback()) {
            $circuit_breaker->set_default_fallback($settings->get_default_fallback());
        }
        return $circuit_breaker;
    }
}