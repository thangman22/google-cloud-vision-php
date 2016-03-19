<?php
namespace GoogleCloudVisionPHP;

use Exception;

class GoogleCloudVision
{
    private $features = array();

    private $imageContext = array();

    private $image = array();

    private $requestBody = array();

    private $version = "v1";

    private $urlEnpoint = "https://vision.googleapis.com/";

    private $key;

    public function setImage($path, $type = "FILE")
    {
        if (empty($path)) {
            throw new Exception("Path valiable is empty.", 1);
        }
        if ($type == "GSC") {
            $this->image['source']['gcs_image_uri'] = $path;
        } elseif ($type == "FILE") {
            $this->image['content'] = $this->convertImgtoBased64($path);
        }
        return $this->setRequestBody();
    }

    public function convertImgtoBased64($path)
    {
        $data = file_get_contents($path);
        return base64_encode($data);
    }

    private function setRequestBody()
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
        if (empty($type)) {
            throw new Exception("Type valiable is empty.", 1);
        }

        if (!is_numeric($maxResults)) {
            throw new Exception("maxResults valiable is not valid it should be Integer.", 1);
        }

        $this->features[] = array("type" => $type, "maxResults" => $maxResults);
        return $this->setRequestBody();
    }

    public function setImageContext($imageContext)
    {
        if (empty($imageContext) && !is_array($imageContext)) {
            throw new Exception("imageContext valiable is not valid it should be Array.", 1);
        }
        $this->imageContext = $imageContext;
        return $this->setRequestBody();
    }

    public function addFeatureUnspecified($maxResults)
    {
        return $this->addFeature("TYPE_UNSPECIFIED", $maxResults);
    }
    public function addFeatureFaceDetection($maxResults)
    {
        return $this->addFeature("FACE_DETECTION", $maxResults);
    }
    public function addFeatureLandmarkDetection($maxResults)
    {
        return $this->addFeature("LANDMARK_DETECTION", $maxResults);
    }
    public function addFeatureLogoDetection($maxResults)
    {
        return $this->addFeature("LOGO_DETECTION", $maxResults);
    }
    public function addFeatureLabelDetection($maxResults)
    {
        return $this->addFeature("LABEL_DETECTION", $maxResults);
    }
    public function addFeatureOCR($maxResults)
    {
        return $this->addFeature("TEXT_DETECTION", $maxResults);
    }
    public function addFeatureSafeSeachDetection($maxResults)
    {
        return $this->addFeature("SAFE_SEARCH_DETECTION", $maxResults);
    }
    public function addFeatureImageProperty($maxResults)
    {
        return $this->addFeature("IMAGE_PROPERTIES", $maxResults);
    }

    public function request($endpoint = "annotate")
    {
        if (empty($this->key)) {
            throw new Exception("API Key is empty Please grant from https://console.cloud.google.com/apis/credentials", 1);
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

    private function requestServer($url, $data)
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
