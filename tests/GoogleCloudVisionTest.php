<?php
use GoogleCloudVisionPHP\GoogleCloudVision;

/**
 * Class GoogleCloudVisionTest
 * @covers GoogleCloudVisionPHP\GoogleCloudVision
 */
class GoogleCloudVisionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var GoogleCloudVision
     */
    protected $gcv;

    /**
     * @var string
     */
    protected $filePath;

    protected function setUp()
    {
        $this->filePath = 'https://yt3.ggpht.com/-v0soe-ievYE/AAAAAAAAAAI/AAAAAAAAAAA/OixOH_h84Po/s900-c-k-no-mo-rj-c0xffffff/photo.jpg';
        $this->gcv = new GoogleCloudVision();
    }

    public function testConvertImgtoBased64()
    {
        $this->filePath = './examples/images/eiffel-tower.jpg';
        $countbase64 = strlen($this->gcv->convertImgtoBased64($this->filePath));
        $this->assertEquals($countbase64, 367996);
    }

    public function testSetImageWithFile()
    {
        $this->filePath = './examples/images/eiffel-tower.jpg';
        $request = $this->gcv->setImage($this->filePath);
        $this->assertNotNull($request['requests'][0]['image']['content']);
    }

    public function testSetRawImage()
    {
        $this->filePath = './examples/images/eiffel-tower.jpg';
        $request = $this->gcv->setImage(file_get_contents($this->filePath),'RAW');
        $this->assertEquals(367996,strlen($request['requests'][0]['image']['content']));
    }

    public function testSetImageWithGcs()
    {
        $this->filePath = 'gs://BUCKET/path_img.jpg';
        $request = $this->gcv->setImage($this->filePath);
        $this->assertNotNull($request['requests'][0]['image']['source']['imageUri']);
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

    /**
     * @expectedException Exception
     */
    public function testSetImageException()
    {
        $request = $this->gcv->addFeature("dddd","dddd");
    }
    /**
     * @expectedException Exception
     */
    public function testSetImageContextException()
    {
        $request = $this->gcv->setImageContext("dddd");
    }

    /**
     * @expectedException Exception
     */
    public function testRequestWithoutKey()
    {
        $this->gcv->setImage($this->filePath);
        $this->gcv->addFeature("LABEL_DETECTION", 1);
        $response = $this->gcv->request();
    }

    /**
     * @expectedException Exception
     */
    public function testRequestWithoutData()
    {
        $this->gcv->setKey(getenv('GCV_KEY'));
        $response = $this->gcv->request();
    }

    //Integration Test
    public function testRequest()
    {
        $this->gcv->setKey(getenv('GCV_KEY'));
        $this->gcv->setImage($this->filePath);
        $this->gcv->addFeature("LABEL_DETECTION", 1);

        $response = $this->gcv->request();
        $this->assertNotNull($response['responses']);
    }
}
