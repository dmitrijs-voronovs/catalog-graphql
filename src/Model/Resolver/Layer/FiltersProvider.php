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

namespace ScandiPWA\CatalogGraphQl\Model\Resolver\Layer;

use Magento\Catalog\Model\Layer\FilterListFactory;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\CatalogGraphQl\Model\Resolver\Layer\FilterableAttributesListFactory;

/**
 * Layer types filters provider.
 */
class FiltersProvider extends \Magento\CatalogGraphQl\Model\Resolver\Layer\FiltersProvider
{
    private $filterPositions;

    /**
     * @var Resolver
     */
    private $layerResolver;

    /**
     * @var FilterableAttributesListFactory
     */
    private $filterableAttributesListFactory;

    /**
     * @var FilterListFactory
     */
    private $filterListFactory;

    /**
     * @param Resolver $layerResolver
     * @param FilterableAttributesListFactory $filterableAttributesListFactory
     * @param FilterListFactory $filterListFactory
     */
    public function __construct(
        Resolver $layerResolver,
        FilterableAttributesListFactory $filterableAttributesListFactory,
        FilterListFactory $filterListFactory
    ) {
        $this->layerResolver = $layerResolver;
        $this->filterableAttributesListFactory = $filterableAttributesListFactory;
        $this->filterListFactory = $filterListFactory;
    }

    /**
     * Get layer type filters.
     *
     * @param string $layerType
     * @return array
     */
    public function getFilters(string $layerType) : array
    {
        $filterableAttributesList = $this->filterableAttributesListFactory->create(
            $layerType
        );
        $filterList = $this->filterListFactory->create(
            [
                'filterableAttributes' => $filterableAttributesList
            ]
        );
        $filterData = $filterList->getFilters($this->layerResolver->get());
        $this->filterPositions = $filterData['positions'];
        return $filterData['filters'];
    }

    /**
     * Get filter positions.
     *
     * @return array
     */
    public function getFilterPositions() : array
    {
        return $this->filterPositions;
    }
}
