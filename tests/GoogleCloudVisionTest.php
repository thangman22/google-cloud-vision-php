<?php
use GoogleCloudVisionPHP\GoogleCloudVision;

class GoogleCloudVisionTest extends PHPUnit_Framework_TestCase
{

    protected $gcv;

    protected $filePath = __DIR__.'/./dog.jpg';

    protected function setUp()
    {
        $this->gcv = new GoogleCloudVision();
    }
    public function testConvertImgtoBased64()
    {
        $countbase64 = strlen($this->gcv->convertImgtoBased64($this->filePath));
        $this->assertEquals($countbase64, 30420);
    }

    public function testSetImageWithFile()
    {
        $request = $this->gcv->setImage($this->filePath);
        $this->assertNotNull($request['requests'][0]['image']['content']);
    }

    public function testSetImageWithGsc()
    {
        $request = $this->gcv->setImage($this->filePath, "GSC");
        $this->assertNotNull($request['requests'][0]['image']['source']['gcs_image_uri']);
    }

    public function testAddType()
    {
        $request = $this->gcv->addFeature("LABEL_DETECTION", 1);
        $this->assertEquals($request['requests'][0]['features'][0]['type'],"LABEL_DETECTION");
    }

    public function testSetImageContext()
    {
        $request = $this->gcv->setImageContext(array("languageHints"=>array("th","en")));
        $this->assertEquals($request['requests'][0]['imageContext']['languageHints'][0],"th");
    }

    public function testRequest()
    {
        require_once __DIR__.'/./config.php';
        $this->gcv->setKey($config['key']);
        $this->gcv->setImage($this->filePath);
        $this->gcv->addFeature("LABEL_DETECTION", 1);

        var_dump($this->gcv->request());
    }
}
