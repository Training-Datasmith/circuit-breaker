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
use Presta_Shop\Circuit_Breaker\Contract\Storage_Interface;
use Presta_Shop\Circuit_Breaker\Contract\System_Interface;
use Presta_Shop\Circuit_Breaker\Transition\Event_Dispatcher;
use Symfony\Component\Event_Dispatcher\Event_Dispatcher_Interface;
/**
 * Symfony implementation of Circuit Breaker.
 */
final class Symfony_Circuit_Breaker extends Advanced_Circuit_Breaker
{
    public function __construct(System_Interface $system, Client_Interface $client, Storage_Interface $storage, Event_Dispatcher_Interface $event_dispatcher)
    {
        parent::__construct($system, $client, $storage, new Event_Dispatcher($event_dispatcher));
    }
}