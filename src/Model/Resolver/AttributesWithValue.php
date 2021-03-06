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

namespace ScandiPWA\CatalogGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Swatches\Helper\Data;
use Magento\Catalog\Api\ProductAttributeManagementInterface;

use Magento\Catalog\Model\ProductRepository;

class AttributesWithValue implements ResolverInterface
{
    /**
     * @var Data
     */
    protected $swatchHelper;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * CustomAttributes constructor.
     * @param Data $swatchHelper
     * @param ProductRepository $productRepository
     */
    public function __construct(
        Data $swatchHelper,
        ProductRepository $productRepository
    ) {
        $this->swatchHelper = $swatchHelper;
        $this->productRepository = $productRepository;
    }

    protected function getAttributeOptions($attr, $rawOptions) {
        if (!$this->swatchHelper->isSwatchAttribute($attr)) return [];

        $optionIds = array_map(function ($option) { return $option['value']; }, $rawOptions);
        $swatchOptions = $this->swatchHelper->getSwatchesByOptionsId($optionIds);

        return array_map(function ($option) use ($swatchOptions) {
            $option['swatch_data'] = $swatchOptions[$option['value']] ?? [];
            return $option;
        }, $rawOptions);
    }

    /**
     * Fetches the data from persistence models and format it according to the GraphQL schema.
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @throws \Exception
     * @return mixed|Value
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $product = $value['model'];
        $attributesToReturn = [];

        foreach ($product->getAttributes() as $attr) {
            if ($attr->getIsVisibleOnFront()) {
                $rawOptions = $attr->getSource()->getAllOptions(true, true);
                array_shift($rawOptions);

                $attributesToReturn[] = [
                    'attribute_value' => $attr ? $attr->getValue() : null,
                    'attribute_code' => $attr->getAttributeCode(),
                    'attribute_type' => $attr->getFrontendInput(),
                    'attribute_label' => $attr->getFrontendLabel(),
                    'attribute_id' => $attr->getAttributeId(),
                    'attribute_options' => $this->getAttributeOptions($attr, $rawOptions)
                ];
            }
        }

        return $attributesToReturn;
    }
}