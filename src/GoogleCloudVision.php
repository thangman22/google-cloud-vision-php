<?php
namespace GoogleCloudVisionPHP;

use Exception;

/**
 * Class GoogleCloudVision
 * @package GoogleCloudVisionPHP
 */
class GoogleCloudVision
{
    /**
     * @var array
     */
    protected $features = array();

    /**
     * @var array
     */
    protected $imageContext = array();

    /**
     * @var array
     */
    protected $image = array();

    /**
     * @var array
     */
    protected $requestBody = array();

    /**
     * @var string
     */
    protected $version = "v1";

    /**
     * @var string
     */
    protected $endpoint = "https://vision.googleapis.com/";

    /**
     * Only use the 'private_key' given by Google
     *
     * @var string
     */
    protected $key;

    /**
     * @param string $newEndpoint
     */
    public function setEndpoint($newEndpoint)
    {
        $this->endpoint = $newEndpoint;
    }

    /**
     * Input can either be a string for GSC URL
     * or the actual raw Image
     *
     * @param $input
     * @param string $type
     *
     * @return array
     */
    public function setImage($input, $type = "FILE")
    {
        if ($type == "GSC") {
            $this->image['source']['gcs_image_uri'] = $input;
        } elseif ($type == "FILE") {
            $this->image['content'] = $this->convertImgtoBased64($input);
        } elseif($type == "RAW") {
            $this->image['content'] = base64_encode($input);
        }
        return $this->setRequestBody();
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function convertImgtoBased64($path)
    {
        $data = file_get_contents($path);
        return base64_encode($data);
    }

    /**
     * @return array
     */
    protected function setRequestBody()
    {
        if (!empty($this->image)) {
            $this->requestBody['requests'][0]['image'] = $this->image;
        }

        if (!empty($this->features)) {
            $this->requestBody['requests'][0]['features'] = $this->features;
        }

        if (!empty($this->imageContext)) {
            $this->requestBody['requests'][0]['imageContext'] = $this->imageContext;
        }

        return $this->requestBody;
    }

    public function addFeature($type, $maxResults = 1)
    {

        if (!is_numeric($maxResults)) {
            throw new Exception("maxResults variable is not valid it should be Integer.", 1);
        }

        $this->features[] = array("type" => $type, "maxResults" => $maxResults);
        return $this->setRequestBody();
    }

    /**
     * @param $imageContext
     *
     * @return array
     * @throws Exception
     */
    public function setImageContext($imageContext)
    {
        if (!is_array($imageContext)) {
            throw new Exception("imageContext variable is not valid it should be Array.", 1);
        }

        $this->imageContext = $imageContext;
        return $this->setRequestBody();
    }

    /**
     * @param int $maxResults
     *
     * @return array
     * @throws Exception
     */
    public function addFeatureUnspecified($maxResults = 1)
    {
        return $this->addFeature("TYPE_UNSPECIFIED", $maxResults);
    }

    /**
     * @param int $maxResults
     *
     * @return array
     * @throws Exception
     */
    public function addFeatureFaceDetection($maxResults = 1)
    {
        return $this->addFeature("FACE_DETECTION", $maxResults);
    }

    /**
     * @param int $maxResults
     *
     * @return array
     * @throws Exception
     */
    public function addFeatureLandmarkDetection($maxResults = 1)
    {
        return $this->addFeature("LANDMARK_DETECTION", $maxResults);
    }

    /**
     * @param int $maxResults
     *
     * @return array
     * @throws Exception
     */
    public function addFeatureLogoDetection($maxResults = 1)
    {
        return $this->addFeature("LOGO_DETECTION", $maxResults);
    }

    /**
     * @param int $maxResults
     *
     * @return array
     * @throws Exception
     */
    public function addFeatureLabelDetection($maxResults = 1)
    {
        return $this->addFeature("LABEL_DETECTION", $maxResults);
    }

    /**
     * @param int $maxResults
     *
     * @return array
     * @throws Exception
     */
    public function addFeatureOCR($maxResults = 1)
    {
        return $this->addFeature("TEXT_DETECTION", $maxResults);
    }

    /**
     * @param int $maxResults
     *
     * @return array
     * @throws Exception
     */
    public function addFeatureSafeSeachDetection($maxResults = 1)
    {
        return $this->addFeature("SAFE_SEARCH_DETECTION", $maxResults);
    }

    /**
     * @param int $maxResults
     *
     * @return array
     * @throws Exception
     */
    public function addFeatureImageProperty($maxResults = 1)
    {
        return $this->addFeature("IMAGE_PROPERTIES", $maxResults);
    }

    /**
     * @param string $endpoint
     *
     * @return mixed
     * @throws Exception
     */
    public function request($endpoint = "annotate")
    {
        if (empty($this->key)) {
            throw new Exception("API Key is empty Please grant from https://console.cloud.google.com/apis/credentials", 1);
        }

        if (empty($this->features)) {
            throw new Exception("Features is can't empty.", 1);
        }

        if (empty($this->image)) {
            throw new Exception("Images is can't empty.", 1);
        }

        $url = $this->endpoint;

        if ($endpoint == "annotate") {
            $url = $this->endpoint . $this->version . "/images:annotate?key=" . $this->key;
        }

        return $this->requestServer($url, $this->requestBody);
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @param string $url
     * @param $data
     *
     * @return array
     * @throws Exception
     */
    protected function requestServer($url, $data)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

        $res = json_decode(curl_exec($ch), true);

        $errors = curl_error($ch);

        if ($errors !== null) {
            throw new Exception($errors);
        }

        return $res;
    }
}
