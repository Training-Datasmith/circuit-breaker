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
namespace Presta_Shop\Circuit_Breaker\Transition;

use Presta_Shop\Circuit_Breaker\Contract\Transition_Dispatcher_Interface;
use Presta_Shop\Circuit_Breaker\Event\Transition_Event;
use Symfony\Component\Event_Dispatcher\Event_Dispatcher_Interface;
/**
 * Class EventDispatcher implements the TransitionDispatcher using the Symfony EventDispatcherInterface
 */
class Event_Dispatcher implements Transition_Dispatcher_Interface
{
    /**
     * @var EventDispatcherInterface the Symfony Event Dispatcher
     */
    private $event_dispatcher;
    public function __construct(Event_Dispatcher_Interface $event_dispatcher)
    {
        $this->event_dispatcher = $event_dispatcher;
    }
    /**
     * {@inheritdoc}
     */
    public function dispatch_transition(string $transition, string $service, array $service_parameters): void
    {
        $event = new Transition_Event($transition, $service, $service_parameters);
        $this->event_dispatcher->dispatch($transition, $event);
    }
}