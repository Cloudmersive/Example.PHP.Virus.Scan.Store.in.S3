<?php

$contents = $_FILES['image'];
$file = 'php://memory'; //full memory buffering mode
//$file = 'php://temp/maxmemory:1048576'; //partial memory buffering mode. Tries to use memory, but over 1MB will automatically page the excess to a file.
$o = new SplFileObject($file, 'w+');
$o->fwrite($contents);

// Step 1 - Scan for viruses with Cloudmersive Virus Scan

require_once(__DIR__ . '/vendor/autoload.php');

// Configure API key authorization: Apikey
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setApiKey('Apikey', 'YOUR_API_KEY');



$apiInstance = new Swagger\Client\Api\ScanApi(
    new GuzzleHttp\Client(),
    $config
);
$input_file = $o; // \SplFileObject | Input file to perform the operation on.

$result = $apiInstance->scanFile($input_file);

// Step 2 - Upload to S3

	if(isset($_FILES['image'])){
		$file_name = $_FILES['image']['name'];   
		$temp_file_location = $_FILES['image']['tmp_name']; 

		require 'vendor/autoload.php';

		$s3 = new Aws\S3\S3Client([
			'region'  => '-- your region --',
			'version' => 'latest',
			'credentials' => [
				'key'    => "-- access key id --",
				'secret' => "-- secret access key --",
			]
		]);		

		$result = $s3->putObject([
			'Bucket' => '-- bucket name --',
			'Key'    => $file_name,
			'SourceFile' => $temp_file_location			
		]);

		var_dump($result);
	}
?>

<form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">         
	<input type="file" name="image" />
	<input type="submit"/>
</form>      