<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Helpers\ImageHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageHeplerTest extends TestCase
{
    protected $folder = 'testImage';

    protected $file_name_test;

    protected $fake_image;

    protected $storage;

    public function setUp(): void {
        parent::setUp();
        Storage::fake('public');
        $this->storage = Storage::disk('public');
        $this->fake_image = UploadedFile::fake()->image('test.jpg');
        $this->file_name_test = ImageHelper::saveImageStorage($this->fake_image, $this->folder);
    }
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSaveImageToStorage() {
        $file_name = ImageHelper::saveImageStorage($this->fake_image, $this->folder);
        $this->storage->assertExists($this->folder .'/' . $file_name);
    }

    public function testGetImage(){
        $this->storage->assertExists($this->folder .'/' . $this->file_name_test);
        $image = ImageHelper::getImage($this->file_name_test, $this->folder);
        $this->assertStringEndsWith($this->folder .'/' . $this->file_name_test, $image);
    }

    public function testGetImageInfo() {
        $this->storage->assertExists($this->folder .'/' . $this->file_name_test);
        $imageInfo = ImageHelper::getImageInfo($this->file_name_test, $this->folder);
        $this->assertIsArray($imageInfo);
        $this->assertArrayHasKey('name' ,$imageInfo);
        $this->assertArrayHasKey('size' ,$imageInfo);
        $this->assertArrayHasKey('path' ,$imageInfo);
    }

    public function testNotGetImageInfo() {
        $this->storage->assertMissing($this->folder .'/' . 'no_image.jpg');
        $imageInfo = ImageHelper::getImageInfo('no_image.jpg', $this->folder);
        $this->assertIsArray($imageInfo);
        $this->assertArrayHasKey('name' ,$imageInfo);
        $this->assertArrayNotHasKey('size' ,$imageInfo);
        $this->assertArrayNotHasKey('path' ,$imageInfo);
    }

    public function testRemoveImageStorage() {
        $this->storage->assertExists($this->folder .'/' . $this->file_name_test);
        ImageHelper::removeImage($this->file_name_test, $this->folder);
        $this->storage->assertMissing($this->folder .'/' . $this->file_name_test);
    }
}
