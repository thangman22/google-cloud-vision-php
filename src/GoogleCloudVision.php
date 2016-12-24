<?php
namespace GoogleCloudVisionPHP;

use Exception;

class GoogleCloudVision
{
    protected $features = array();

    protected $imageContext = array();

    protected $image = array();

    protected $requestBody = array();

    protected $version = "v1";

    protected $urlEnpoint = "https://vision.googleapis.com/";

    protected $key;

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

    public function convertImgtoBased64($path)
    {
        $urlParts = pathinfo($path);
        $extension = $urlParts['extension'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $base64 = 'data:image/' . $extension . ';base64,' . base64_encode($response);
        return $base64;
    }

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

    public function setImageContext($imageContext)
    {
        if (!is_array($imageContext)) {
            throw new Exception("imageContext variable is not valid it should be Array.", 1);
        }
        $this->imageContext = $imageContext;
        return $this->setRequestBody();
    }

    public function addFeatureUnspecified($maxResults = 1)
    {
        return $this->addFeature("TYPE_UNSPECIFIED", $maxResults);
    }
    public function addFeatureFaceDetection($maxResults = 1)
    {
        return $this->addFeature("FACE_DETECTION", $maxResults);
    }
    public function addFeatureLandmarkDetection($maxResults = 1)
    {
        return $this->addFeature("LANDMARK_DETECTION", $maxResults);
    }
    public function addFeatureLogoDetection($maxResults = 1)
    {
        return $this->addFeature("LOGO_DETECTION", $maxResults);
    }
    public function addFeatureLabelDetection($maxResults = 1)
    {
        return $this->addFeature("LABEL_DETECTION", $maxResults);
    }
    public function addFeatureOCR($maxResults = 1)
    {
        return $this->addFeature("TEXT_DETECTION", $maxResults);
    }
    public function addFeatureSafeSeachDetection($maxResults = 1)
    {
        return $this->addFeature("SAFE_SEARCH_DETECTION", $maxResults);
    }
    public function addFeatureImageProperty($maxResults = 1)
    {
        return $this->addFeature("IMAGE_PROPERTIES", $maxResults);
    }

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

        if ($endpoint == "annotate") {
            $url = $this->urlEnpoint . $this->version . "/images:annotate?key=" . $this->key;
        }
        return $this->requestServer($url, $this->requestBody);
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

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
        return $res;
    }

}
