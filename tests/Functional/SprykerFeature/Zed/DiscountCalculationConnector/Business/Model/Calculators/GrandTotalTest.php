<?php

namespace Functional\SprykerFeature\Zed\DiscountCalculationConnector\Business\Model\Calculator;

use Codeception\TestCase\Test;
use Generated\Shared\Transfer\Calculation\DependencyTotalsInterfaceTransfer;
use Generated\Shared\Transfer\CalculationTotalsTransfer;
use SprykerEngine\Shared\Kernel\LocatorLocatorInterface;
use Generated\Shared\Transfer\SalesOrderTransfer;
use Generated\Shared\Transfer\SalesOrderItemTransfer;
use Generated\Shared\Transfer\CalculationDiscountTransfer;
use Generated\Shared\Transfer\CalculationExpenseTransfer;
use SprykerFeature\Zed\Calculation\Business\Model\Calculator\GrandTotalTotalsCalculator;
use SprykerFeature\Zed\DiscountCalculationConnector\Business\Model\Calculator\DiscountTotalsCalculator;
use SprykerFeature\Zed\Calculation\Business\Model\Calculator\ExpenseTotalsCalculator;
use SprykerFeature\Zed\Calculation\Business\Model\Calculator\SubtotalTotalsCalculator;
use SprykerFeature\Zed\DiscountCalculationConnector\Business\Model\Calculator\GrandTotalWithDiscountsTotalsCalculator;
use SprykerEngine\Zed\Kernel\Locator;

/**
 * Class GrandTotalWithoutDiscountsTest
 * @group GrandTotalWithoutDiscountsTest
 * @group Calculation
 * @package PhpUnit\SprykerFeature\Zed\Calculation\Business\Model\Calculator
 */
class GrandTotalTest extends Test
{
    const ITEM_GROSS_PRICE = 10000;
    const ITEM_COUPON_DISCOUNT_AMOUNT = 1000;
    const ITEM_SALESRULE_DISCOUNT_AMOUNT = 1000;
    const ORDER_SHIPPING_COSTS = 2000;

    /**
     * @var LocatorLocatorInterface|\Generated\Zed\Ide\AutoCompletion
     */
    protected $locator;

    protected function setUp()
    {
        parent::setUp();
        $this->locator = Locator::getInstance();
    }

    public function testGrandTotalShouldBeZeroForAnEmptyOrder()
    {
        $order = $this->getOrderWithFixtureData();

        $totalsTransfer = $this->getPriceTotals();
        $calculator = $this->getGrandTotalCalculator();
        $calculator->recalculateTotals($totalsTransfer, $order, $order->getItems());
        $this->assertEquals(0, $totalsTransfer->getGrandTotalWithDiscounts());
    }

    public function testGrandTotalWithoutDiscountsShouldNotBeReducedByTheDiscounts()
    {
        $order = $this->getOrderWithFixtureData();

        $item = $this->getItemWithFixtureData();
        $item->setGrossPrice(self::ITEM_GROSS_PRICE);
        $order->addItem($item);

        $discount = $this->getPriceDiscount();
        $discount->setAmount(self::ITEM_SALESRULE_DISCOUNT_AMOUNT);
        $item->addDiscount($discount);

        $totalsTransfer = $this->getPriceTotals();
        $calculator = $this->getGrandTotalCalculator();
        $calculator->recalculateTotals($totalsTransfer, $order, $order->getItems());
        $this->assertEquals(self::ITEM_GROSS_PRICE, $totalsTransfer->getGrandTotal());
    }

    public function testGrandTotalWithoutDiscountsShouldBeByTheDiscountAmountReducedComparedToTheGrandTotal()
    {
        $order = $this->getOrderWithFixtureData();

        $item = $this->getItemWithFixtureData();
        $item->setGrossPrice(self::ITEM_GROSS_PRICE);
        $order->addItem($item);

        $discount = $this->getPriceDiscount();
        $discount->setAmount(self::ITEM_SALESRULE_DISCOUNT_AMOUNT);
        $item->addDiscount($discount);

        $totalsTransfer = $this->getPriceTotals();
        $calculator = $this->getGrandTotalCalculator();
        $calculator->recalculateTotals($totalsTransfer, $order, $order->getItems());
        $calculator = $this->getGrandTotalWithDiscountCalculator();
        $calculator->recalculateTotals($totalsTransfer, $order, $order->getItems());
        $this->assertEquals(
            $totalsTransfer->getGrandTotal() - self::ITEM_SALESRULE_DISCOUNT_AMOUNT,
            $totalsTransfer->getGrandTotalWithDiscounts()
        );
    }

    /**
     * @return GrandTotalTotalsCalculator
     */
    private function getGrandTotalCalculator()
    {
        return new GrandTotalTotalsCalculator(
            new SubtotalTotalsCalculator(),
            new ExpenseTotalsCalculator()
        );
    }

    /**
     * @return GrandTotalWithDiscountsTotalsCalculator
     */
    protected function getGrandTotalWithDiscountCalculator()
    {
        return new GrandTotalWithDiscountsTotalsCalculator(
            $this->locator->calculation()->facade(),
            new DiscountTotalsCalculator()
        );
    }

    /**
     * @return CalculationTotalsTransfer
     */
    protected function getPriceTotals()
    {
        return new CalculationTotalsTransfer();
    }

    /**
     * @return CalculationDiscountTransfer
     */
    protected function getPriceDiscount()
    {
        return new CalculationDiscountTransfer();
    }

    /**
     * @return SalesOrderTransfer
     */
    protected function getOrderWithFixtureData()
    {
        $order = new SalesOrderTransfer();

        return $order;
    }

    /**
     * @return SalesOrderItemTransfer
     */
    protected function getItemWithFixtureData()
    {
        $item = new SalesOrderItemTransfer();

        return $item;
    }

    /**
     * @return CalculationExpenseTransfer
     */
    protected function getExpenseWithFixtureData()
    {
        $expense = new CalculationExpenseTransfer();

        return $expense;
    }
}
