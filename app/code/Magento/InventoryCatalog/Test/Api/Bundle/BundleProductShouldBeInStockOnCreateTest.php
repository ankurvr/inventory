<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\InventoryCatalog\Test\Api\Bundle;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Bundle\Api\Data\LinkInterface;

class BundleProductShouldBeInStockOnCreateTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductRepositoryV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products';
    const BUNDLE_PRODUCT_SKU = 'SKU-1-test-product-bundle';

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $productCollection;

    /**
     * Execute per test initialization
     */
    public function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->productCollection = $objectManager->get(\Magento\Catalog\Model\ResourceModel\Product\Collection::class);
    }

    /**
     * Execute per test cleanup
     */
    public function tearDown()
    {
        $resourcePath = self::RESOURCE_PATH . '/' . self::BUNDLE_PRODUCT_SKU;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => $resourcePath,
                'httpMethod' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'deleteById',
            ],
        ];
        $requestData = ["sku" => self::BUNDLE_PRODUCT_SKU];
        $this->_webApiCall($serviceInfo, $requestData);

        parent::tearDown();
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Magento/InventoryApi/Test/_files/products.php
     */
    public function testIsBundleProductWithSimpleProductInStockAfterCreate()
    {
        $bundleProductOptions = [
            [
                "title" => "Test simple",
                "type" => "select",
                "required" => false,
                "product_links" => [
                    [
                        "sku" => 'SKU-1',
                        "qty" => 1.5,
                        'is_default' => false,
                        'price' => 1.0,
                        'price_type' => LinkInterface::PRICE_TYPE_FIXED,
                    ],
                ],
            ],
        ];

        $product = [
            "sku" => self::BUNDLE_PRODUCT_SKU,
            "name" => 'bundle product',
            "type_id" => "bundle",
            "price" => 50,
            'attribute_set_id' => 4,
            "custom_attributes" => [
                "price_type" => [
                    'attribute_code' => 'price_type',
                    'value' => \Magento\Bundle\Model\Product\Price::PRICE_TYPE_FIXED
                ],
                "price_view" => [
                    "attribute_code" => "price_view",
                    "value" => "1",
                ],
            ],
            "extension_attributes" => [
                "bundle_product_options" => $bundleProductOptions,
            ],
        ];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Save',
            ],
        ];
        $requestData = ['product' => $product];
        $response = $this->_webApiCall($serviceInfo, $requestData);

        $this->assertEquals(self::BUNDLE_PRODUCT_SKU, $response[ProductInterface::SKU]);
        $this->assertEquals(1, $response['extension_attributes']['stock_item']['is_in_stock']);
    }
}
