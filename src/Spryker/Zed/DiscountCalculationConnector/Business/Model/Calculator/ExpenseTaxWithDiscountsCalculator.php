<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\DiscountCalculationConnector\Business\Model\Calculator;

use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\DiscountCalculationConnector\Dependency\Facade\DiscountCalculationToTaxInterface;

class ExpenseTaxWithDiscountsCalculator implements CalculatorInterface
{

    /**
     * @var int
     */
    protected $roundingError = 0;

    /**
     * @var DiscountCalculationToTaxInterface
     */
    protected $taxFacade;

    /**
     * @param DiscountCalculationToTaxInterface $taxFacade
     */
    public function __construct(DiscountCalculationToTaxInterface $taxFacade)
    {
        $this->taxFacade = $taxFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    public function recalculate(QuoteTransfer $quoteTransfer)
    {
        $this->addExpenseTaxes($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    protected function addExpenseTaxes(QuoteTransfer $quoteTransfer)
    {
        foreach ($quoteTransfer->getExpenses() as $expenseTransfer) {
            if (!$expenseTransfer->getTaxRate()) {
                continue;
            }
            $itemUnitTaxAmount = $this->calculateTaxAmount(
                $expenseTransfer->getUnitGrossPriceWithDiscounts(),
                $expenseTransfer->getTaxRate()
            );

            $expenseTransfer->setUnitTaxAmountWithDiscounts($itemUnitTaxAmount);

            $itemSumTaxAmount = $this->calculateTaxAmount(
                $expenseTransfer->getSumGrossPriceWithDiscounts(),
                $expenseTransfer->getTaxRate()
            );

            $expenseTransfer->setSumTaxAmountWithDiscounts($itemSumTaxAmount);

        }
    }

    /**
     * @param int $price
     * @param float $taxRate
     *
     * @return float
     */
    protected function calculateTaxAmount($price, $taxRate)
    {
        $taxAmount = $this->taxFacade->getTaxAmountFromGrossPrice($price, $taxRate, false);

        $taxAmount += $this->roundingError;

        $taxAmountRounded = round($taxAmount, 4);
        $this->roundingError = $taxAmount - $taxAmountRounded;

        return $taxAmountRounded;
    }
}