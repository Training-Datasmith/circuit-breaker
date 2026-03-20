<?php

declare (strict_types=1);
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
namespace Presta_Shop\Circuit_Breaker\Contract;

/**
 * Interface FactorySettingsInterface contains the settings used by the Factory
 */
interface Factory_Settings_Interface
{
    public static function merge(Factory_Settings_Interface $settings_a, Factory_Settings_Interface $settings_b): Factory_Settings_Interface;
    public function get_failures(): int;
    public function get_timeout(): float;
    public function get_threshold(): int;
    public function get_stripped_timeout(): float;
    public function get_stripped_failures(): int;
    public function get_storage(): ?Storage_Interface;
    public function get_dispatcher(): ?Transition_Dispatcher_Interface;
    public function get_client_options(): array;
    public function get_client(): ?Client_Interface;
    public function get_default_fallback(): ?callable;
}