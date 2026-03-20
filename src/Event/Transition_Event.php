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
namespace Presta_Shop\Circuit_Breaker\Event;

use Symfony\Component\Event_Dispatcher\Event;
class Transition_Event extends Event
{
    /**
     * @var string the Transition name
     */
    private $event_name;
    /**
     * @var string the Service URI
     */
    private $service;
    /**
     * @var array the Service parameters
     */
    private $parameters;
    /**
     * @param string $eventName the transition name
     * @param string $service the Service URI
     * @param array $parameters the Service parameters
     */
    public function __construct(string $event_name, string $service, array $parameters)
    {
        $this->event_name = $event_name;
        $this->service = $service;
        $this->parameters = $parameters;
    }
    /**
     * @return string the Transition name
     */
    public function get_event(): string
    {
        return $this->event_name;
    }
    /**
     * @return string the Service URI
     */
    public function get_service(): string
    {
        return $this->service;
    }
    /**
     * @return array the Service parameters
     */
    public function get_parameters(): array
    {
        return $this->parameters;
    }
}