<?php
/**
 * ScandiPWA_CatalogGraphQl
 *
 * @category    ScandiPWA
 * @package     ScandiPWA_CatalogGraphQl
 * @author      <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Ltd (https://scandiweb.com)
 */
declare(strict_types=1);

namespace ScandiPWA\CatalogGraphQl\Model\Resolver\Layer\DataProvider;

use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\CatalogGraphQl\Model\Resolver\Layer\FiltersProvider;

/**
 * Layered navigation filters data provider.
 */
class Filters extends \Magento\CatalogGraphQl\Model\Resolver\Layer\DataProvider\Filters
{
    /**
     * @var FiltersProvider
     */
    private $filtersProvider;

    /**
     * Filters constructor.
     * @param FiltersProvider $filtersProvider
     */
    public function __construct(
        FiltersProvider $filtersProvider
    ) {
        $this->filtersProvider = $filtersProvider;
    }

    /**
     * Get layered navigation filters data
     *
     * @param string $layerType
     * @return array
     */
    public function getData(string $layerType) : array
    {
        $filtersData = [];
        /** @var AbstractFilter $filter */
        $filters = $this->filtersProvider->getFilters($layerType);
        $filterPositions = $this->filtersProvider->getFilterPositions();

        foreach ($filters as $filter) {
            if ($filter->getItemsCount()) {
                $filterGroup = [
                    'name' => (string)$filter->getName(),
                    'filter_items_count' => $filter->getItemsCount(),
                    'request_var' => $filter->getRequestVar(),
                    'position' => $filterPositions[$filter->getRequestVar()] ?: 0
                ];
                /** @var \Magento\Catalog\Model\Layer\Filter\Item $filterItem */
                foreach ($filter->getItems() as $filterItem) {
                    $filterGroup['filter_items'][] = [
                        'label' => (string)$filterItem->getLabel(),
                        'value_string' => $filterItem->getValueString(),
                        'items_count' => $filterItem->getCount()
                    ];
                }
                $filtersData[] = $filterGroup;
            }
        }
        return $filtersData;
    }
}
