[![Build Status](https://travis-ci.org/thangman22/google-cloud-vision-php.svg?branch=master)](https://travis-ci.org/thangman22/google-cloud-vision-php)
# GoogleCloudVisionPHP
This project hosts the PHP library for the various RESTful based Google Cloud Vision API(s) [Read about Google Cloud Vision API] (https://cloud.google.com/vision/)

## Features
*   Support almost feature of Google Cloud Vision API (Version 1)
*   Auto encode images to based64

## How to get service key
[Google Cloud Vision API Document](https://cloud.google.com/vision/docs/getting-started)

## Requirements
*   PHP >= 5.4 
*   [cURL extension](http://php.net/manual/en/book.curl.php)

## Installation
Add this to your composer.json

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
- Remote HTTP file
- Raw Image
- GCS

```php
// Remote file path
$gcv->setImage("[File path]");

// Local file path
$raw_file = file_get_contents('/LOCAL/FILE/PATH');
$gcv->setImage($raw_file, 'RAW');
```
** Add Features **
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

$response = $gcv->request();

```

