<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\InventorySales\Model;

use Magento\InventorySales\Api\ShippingAlgorithmResultInterface;

class DefaultShippingAlgorithmResult implements ShippingAlgorithmResultInterface
{
    /**
     * @var array
     */
    private $sourceSelections;

    /**
     * DefaultShippingAlgorithmResult constructor.
     * @param array $sourceSelections
     */
    public function __construct(array $sourceSelections)
    {
        $this->sourceSelections = $sourceSelections;
    }

    /**
     * @inheritdoc
     */
    public function getSourceSelections(): array
    {
        return $this->sourceSelections;
    }
}
