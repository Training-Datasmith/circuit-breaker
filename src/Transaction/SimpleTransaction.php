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
namespace Presta_Shop\Circuit_Breaker\Transaction;

use DateTime;
use Presta_Shop\Circuit_Breaker\Contract\Place_Interface;
use Presta_Shop\Circuit_Breaker\Contract\Transaction_Interface;
use Presta_Shop\Circuit_Breaker\Exception\Invalid_Transaction_Exception;
use Presta_Shop\Circuit_Breaker\Util\Assert;
/**
 * Main implementation of Circuit Breaker transaction.
 */
final class Simple_Transaction implements Transaction_Interface
{
    /**
     * @var string the URI of the service
     */
    private $service;
    /**
     * @var int the failures when we call the service
     */
    private $failures;
    /**
     * @var string the Circuit Breaker state
     */
    private $state;
    /**
     * @var DateTime the Transaction threshold datetime
     */
    private $threshold_date_time;
    /**
     * @param string $service the service URI
     * @param int $failures the allowed failures
     * @param string $state the circuit breaker state/place
     * @param int $threshold the place threshold
     */
    public function __construct(string $service, int $failures, string $state, int $threshold)
    {
        $this->validate($service, $failures, $state, $threshold);
        $this->service = $service;
        $this->failures = $failures;
        $this->state = $state;
        $this->init_threshold_date_time($threshold);
    }
    /**
     * {@inheritdoc}
     */
    public function get_service(): string
    {
        return $this->service;
    }
    /**
     * {@inheritdoc}
     */
    public function get_failures(): int
    {
        return $this->failures;
    }
    /**
     * {@inheritdoc}
     */
    public function get_state(): string
    {
        return $this->state;
    }
    /**
     * {@inheritdoc}
     */
    public function get_threshold_date_time(): DateTime
    {
        return $this->threshold_date_time;
    }
    /**
     * {@inheritdoc}
     */
    public function increment_failures(): bool
    {
        ++$this->failures;
        return true;
    }
    /**
     * Helper to create a transaction from the Place.
     *
     * @param PlaceInterface $place the Circuit Breaker place
     * @param string $service the service URI
     */
    public static function create_from_place(Place_Interface $place, string $service): self
    {
        $threshold = $place->get_threshold();
        return new self($service, 0, $place->get_state(), $threshold);
    }
    /**
     * Set the right DateTime from the threshold value.
     *
     * @param int $threshold the Transaction threshold
     */
    private function init_threshold_date_time(int $threshold): void
    {
        $threshold_date_time = new DateTime();
        $threshold_date_time->modify("+{$threshold} second");
        $this->threshold_date_time = $threshold_date_time;
    }
    /**
     * Ensure the transaction is valid (PHP5 is permissive).
     *
     * @param string $service the service URI
     * @param int $failures the failures should be a positive value
     * @param string $state the Circuit Breaker state
     * @param int $threshold the threshold should be a positive value
     *
     * @return bool true if valid
     *
     * @throws InvalidTransactionException
     */
    private function validate(string $service, int $failures, string $state, int $threshold): bool
    {
        $assertions_are_valid = Assert::is_uri($service) && Assert::is_positive_integer($failures) && Assert::is_string($state) && Assert::is_positive_integer($threshold);
        if ($assertions_are_valid) {
            return true;
        }
        throw Invalid_Transaction_Exception::invalid_parameters($service, $failures, $state, $threshold);
    }
}