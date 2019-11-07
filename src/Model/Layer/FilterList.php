<?php
/**
 * ScandiPWA_CatalogGraphQl
 *
 * @category    ScandiPWA
 * @package     ScandiPWA_CatalogGraphQl
 * @author      <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Ltd (https://scandiweb.com)
 */

namespace ScandiPWA\CatalogGraphQl\Model\Layer;

class FilterList extends \Magento\Catalog\Model\Layer\FilterList
{
    /**
     * Retrieve list of filters
     *
     * @param \Magento\Catalog\Model\Layer $layer
     * @return array ['filters' => Filter\AbstractFilter[], 'positions' => String[]]
     */
    public function getFilters(\Magento\Catalog\Model\Layer $layer)
    {
        $positions = [];
        if (!count($this->filters)) {
            $this->filters = [
                $this->objectManager->create($this->filterTypes[self::CATEGORY_FILTER], ['layer' => $layer]),
            ];
            foreach ($this->filterableAttributes->getList() as $attribute) {
                $this->filters[] = $this->createAttributeFilter($attribute, $layer);
                $positions[$attribute->getAttributeCode()] = $attribute->getPosition();
            }
        }

        return [
            'filters' => $this->filters,
            'positions' => $positions
        ];
    }
}
