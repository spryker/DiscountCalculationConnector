<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\DiscountCalculationConnector\Communication\Plugin;

use Generated\Shared\Calculation\OrderInterface;
use Generated\Shared\Calculation\TotalsInterface;
use SprykerFeature\Zed\Calculation\Business\Model\CalculableInterface;
use SprykerFeature\Zed\Calculation\Dependency\Plugin\TotalsCalculatorPluginInterface;
use SprykerFeature\Zed\DiscountCalculationConnector\Communication\DiscountCalculationConnectorDependencyContainer;
use SprykerEngine\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method DiscountCalculationConnectorDependencyContainer getDependencyContainer()
 */
class GrandTotalWithDiscountsTotalsCalculatorPlugin extends AbstractPlugin implements TotalsCalculatorPluginInterface
{
    /**
     * @param TotalsInterface $totalsTransfer
     * @ param OrderInterface $calculableContainer
     * @param CalculableInterface $calculableContainer
     * @param \ArrayObject $calculableItems
     */
    public function recalculateTotals(
        TotalsInterface $totalsTransfer,
        //OrderInterface $calculableContainer,
        CalculableInterface $calculableContainer,
        \ArrayObject $calculableItems
    ) {
        $this
            ->getDependencyContainer()
            ->getDiscountCalculationFacade()
            ->recalculateGrandTotalWithDiscountsTotals($totalsTransfer, $calculableContainer, $calculableItems)
        ;
    }
}
