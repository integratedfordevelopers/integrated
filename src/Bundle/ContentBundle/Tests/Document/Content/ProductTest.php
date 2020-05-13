<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Document\Content;

use Integrated\Bundle\ContentBundle\Document\Content\Product;

class ProductTest extends ContentTest
{
    /**
     * @var Product
     */
    private $product;

    /**
     * Setup the test.
     */
    protected function setUp(): void
    {
        $this->product = new Product();
    }

    /**
     * Test get- and setTitle function.
     */
    public function testGetAndSetTitleFunction()
    {
        $title = 'title';
        $this->assertSame($title, $this->product->setTitle($title)->getTitle());
    }

    /**
     * Test get- and setReference function.
     */
    public function testGetAndSetReferenceFunction()
    {
        $reference = 'reference';
        $this->assertEquals($reference, $this->product->setReference($reference)->getReference());
    }

    /**
     * Test get- and setVariant function.
     */
    public function testGetAndSetVariantFunction()
    {
        $variant = 'variant';
        $this->assertEquals($variant, $this->product->setVariant($variant)->getVariant());
    }

    /**
     * Test get- and setLocale function.
     */
    public function testGetAndSetLocaleFunction()
    {
        $locale = 'locale';
        $this->assertEquals($locale, $this->product->setLocale($locale)->getLocale());
    }

    /**
     * Test get- and setPrice function.
     */
    public function testGetAndSetPriceFunction()
    {
        $price = 10.50;
        $this->assertEquals($price, $this->product->setPrice($price)->getPrice());
    }

    /**
     * Test get- and setStockQuantity function.
     */
    public function testGetAndSetStockQuantityFunction()
    {
        $stock = 15;
        $this->assertEquals($stock, $this->product->setStockQuantity($stock)->getStockQuantity());
    }

    /**
     * Test get- and setOrderable function.
     */
    public function testGetAndSetOrderableFunction()
    {
        $orderable = true;
        $this->assertEquals($orderable, $this->product->setOrderable($orderable)->isOrderable());
    }

    /**
     * Test get- and setContent function.
     */
    public function testGetAndSetContentFunction()
    {
        $content = 'content';
        $this->assertEquals($content, $this->product->setContent($content)->getContent());
    }

    /**
     * Test toString function.
     */
    public function testToStringFunction()
    {
        $title = 'Title';
        $this->assertEquals($title, (string) $this->product->setTitle($title));
    }

    /**
     * {@inheritdoc}
     */
    protected function getContent()
    {
        return $this->product;
    }
}
