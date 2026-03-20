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
use Presta_Shop\Circuit_Breaker\Contract\Factory_Settings_Interface;
use Presta_Shop\Circuit_Breaker\Contract\Storage_Interface;
use Presta_Shop\Circuit_Breaker\Contract\Transition_Dispatcher_Interface;
/**
 * Class FactorySettings is a simple implementation of FactorySettingsInterface, it is mainly
 * a settings container and can be used with any Factory class.
 */
class Factory_Settings implements Factory_Settings_Interface
{
    /** @var int */
    private $failures;
    /** @var float */
    private $timeout;
    /** @var int */
    private $threshold;
    /** @var float */
    private $stripped_timeout;
    /** @var int */
    private $stripped_failures;
    /** @var StorageInterface|null */
    private $storage;
    /** @var TransitionDispatcherInterface|null */
    private $dispatcher;
    /** @var array */
    private $client_options = [];
    /** @var ClientInterface|null */
    private $client;
    /** @var callable|null */
    private $default_fallback;
    public function __construct(int $failures, float $timeout, int $threshold)
    {
        $this->failures = $this->stripped_failures = $failures;
        $this->timeout = $this->stripped_timeout = $timeout;
        $this->threshold = $threshold;
    }
    /**
     * {@inheritdoc}
     */
    public static function merge(Factory_Settings_Interface $settings_a, Factory_Settings_Interface $settings_b): Factory_Settings_Interface
    {
        $merged_settings = new Factory_Settings($settings_b->get_failures(), $settings_b->get_timeout(), $settings_b->get_threshold());
        $merged_settings->set_stripped_failures($settings_b->get_stripped_failures())->set_stripped_timeout($settings_b->get_stripped_timeout());
        $merged_settings->set_client_options(array_merge($settings_a->get_client_options(), $settings_b->get_client_options()));
        if (null !== $settings_b->get_client()) {
            $merged_settings->set_client($settings_b->get_client());
        } elseif (null !== $settings_a->get_client()) {
            $merged_settings->set_client($settings_a->get_client());
        }
        return $merged_settings;
    }
    /**
     * {@inheritdoc}
     */
    public function get_failures(): int
    {
        return $this->failures;
    }
    public function set_failures(int $failures): self
    {
        $this->failures = $failures;
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function get_timeout(): float
    {
        return $this->timeout;
    }
    public function set_timeout(float $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function get_threshold(): int
    {
        return $this->threshold;
    }
    public function set_threshold(int $threshold): self
    {
        $this->threshold = $threshold;
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function get_stripped_timeout(): float
    {
        return $this->stripped_timeout;
    }
    public function set_stripped_timeout(float $stripped_timeout): self
    {
        $this->stripped_timeout = $stripped_timeout;
        return $this;
    }
    public function get_stripped_failures(): int
    {
        return $this->stripped_failures;
    }
    public function set_stripped_failures(int $stripped_failures): self
    {
        $this->stripped_failures = $stripped_failures;
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function get_storage(): ?Storage_Interface
    {
        return $this->storage;
    }
    public function set_storage(Storage_Interface $storage): self
    {
        $this->storage = $storage;
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function get_dispatcher(): ?Transition_Dispatcher_Interface
    {
        return $this->dispatcher;
    }
    public function set_dispatcher(Transition_Dispatcher_Interface $dispatcher): self
    {
        $this->dispatcher = $dispatcher;
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function get_client_options(): array
    {
        return $this->client_options;
    }
    public function set_client_options(array $client_options): self
    {
        $this->client_options = $client_options;
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function get_client(): ?Client_Interface
    {
        return $this->client;
    }
    public function set_client(?Client_Interface $client = null): self
    {
        $this->client = $client;
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function get_default_fallback(): ?callable
    {
        return $this->default_fallback;
    }
    public function set_default_fallback(callable $default_fallback): self
    {
        $this->default_fallback = $default_fallback;
        return $this;
    }
}