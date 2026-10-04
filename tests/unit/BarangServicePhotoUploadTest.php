<?php

namespace Tests\Unit;

use App\Models\BarangModel;
use App\Services\Master\BarangService;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class BarangServicePhotoUploadTest extends CIUnitTestCase
{
    protected string $tempImage;

    protected function setUp(): void
    {
        parent::setUp();
        helper(['primary']);

        // Buat file gambar temporary 600x600 px untuk pengujian kompresi
        $im = imagecreatetruecolor(600, 600);
        $blue = imagecolorallocate($im, 0, 100, 200);
        imagefill($im, 0, 0, $blue);

        $this->tempImage = tempnam(sys_get_temp_dir(), 'test_img_') . '.jpg';
        imagejpeg($im, $this->tempImage, 90);
        imagedestroy($im);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempImage)) {
            @unlink($this->tempImage);
        }
        parent::tearDown();
    }

    public function testProcessPhotoCompressesAndResizesToWebp(): void
    {
        $uploadedFile = new UploadedFile($this->tempImage, 'sample.jpg', 'image/jpeg', filesize($this->tempImage), UPLOAD_ERR_OK, true);

        $service = new BarangService();
        $fileName = $service->processPhoto($uploadedFile);

        $this->assertNotNull($fileName);
        $this->assertStringEndsWith('.webp', $fileName);

        $savedPath = FCPATH . 'uploads/produk/' . $fileName;
        $this->assertFileExists($savedPath);

        // Verifikasi dimensi <= 400x400
        $size = getimagesize($savedPath);
        $this->assertLessThanOrEqual(400, $size[0]);
        $this->assertLessThanOrEqual(400, $size[1]);

        // Verifikasi ukuran berkas di bawah 50 KB
        $this->assertLessThan(50 * 1024, filesize($savedPath));

        // Bersihkan berkas setelah pengujian
        @unlink($savedPath);
    }

    public function testProcessPhotoDeletesOldFileOnUpdate(): void
    {
        // Buat file lama di direktori uploads
        $oldFile = 'old_test_' . uniqid() . '.webp';
        $oldPath = FCPATH . 'uploads/produk/' . $oldFile;
        file_put_contents($oldPath, 'dummy old webp');
        $this->assertFileExists($oldPath);

        $uploadedFile = new UploadedFile($this->tempImage, 'sample2.jpg', 'image/jpeg', filesize($this->tempImage), UPLOAD_ERR_OK, true);

        $service = new BarangService();
        $newFile = $service->processPhoto($uploadedFile, $oldFile);

        $this->assertNotNull($newFile);
        $this->assertFileDoesNotExist($oldPath);

        @unlink(FCPATH . 'uploads/produk/' . $newFile);
    }
}
