[![Build Status](https://travis-ci.org/thangman22/google-cloud-vision-php.svg?branch=master)](https://travis-ci.org/thangman22/google-cloud-vision-php)
# GoogleCloudVisionPHP
This project hosts the PHP library for the various RESTful based Google Cloud Vision API(s) [Read about Google Cloud Vision API](https://cloud.google.com/vision/)

## Features
*   Support almost feature of Google Cloud Vision API (Version 1)
*   Auto encode images to based64

## How to get service key
[Google Cloud Vision API Document](https://cloud.google.com/vision/docs/getting-started)

## Requirements
*   PHP >= 5.4 
*   [cURL extension](http://php.net/manual/en/book.curl.php)

## Installation
This package available on [Packagist](https://packagist.org/packages/thangman22/google-cloud-vision-php), Install the latest version with composer
```
composer require thangman22/google-cloud-vision-php
```

Or add this to your composer.json

```json
"require": {
        "thangman22/google-cloud-vision-php": "*"
    }
```

## Example

Initial GoogleCloudVision
```php
use GoogleCloudVisionPHP\GoogleCloudVision;

$gcv = new GoogleCloudVision();

// Follow instruction from Google Cloud Vision Document
$gcv->setKey("[Key from Google]");
```

**Set Image**

Available image file type
- Remote image file (HTTP, HTTPS and Google cloud storage)
- Local image file

```php
// Remote HTTP image
$gcv->setImage("https://raw.githubusercontent.com/thangman22/google-cloud-vision-php/master/examples/images/faulkner.jpg");

// Google cloud storage
$gcv->setImage("gs://BUCKET/image_path.jpg");

// Local file path
$gcv->setImage('/LOCAL/FILE/PATH.jpg');
```

**Add Features Detections**
```php
// 1 is Max result
$gcv->addFeature("LABEL_DETECTION", 1);

$gcv->addFeatureUnspecified(1);
$gcv->addFeatureFaceDetection(1);
$gcv->addFeatureLandmarkDetection(1);
$gcv->addFeatureLogoDetection(1);
$gcv->addFeatureLabelDetection(1);
$gcv->addFeatureOCR(1);
$gcv->addFeatureSafeSeachDetection(1);
$gcv->addFeatureImageProperty(1);

//Optinal
$gcv->setImageContext(array("languageHints"=>array("th")));

// Request to API
$response = $gcv->request();

```

