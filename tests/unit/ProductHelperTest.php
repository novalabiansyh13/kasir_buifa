<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ProductHelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper(['primary']);
    }

    public function testGetProductImageReturnsUploadedPhotoPathIfExists(): void
    {
        $foto = 'test_sample.webp';
        $path = FCPATH . 'uploads/produk/' . $foto;
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        file_put_contents($path, 'dummy content');

        $url = getProductImage($foto, 1);
        $this->assertStringContainsString('uploads/produk/test_sample.webp', $url);

        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function testGetProductImageReturnsCategoryDefaultWhenPhotoEmpty(): void
    {
        $url = getProductImage(null, 1);
        $this->assertStringContainsString('images/categories/', $url);
    }

    public function testGetProductImageReturnsFallbackWhenCategoryUnknown(): void
    {
        $url = getProductImage(null, 9999);
        $this->assertStringContainsString('images/default-product.webp', $url);
    }
}
