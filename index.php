<?php

if(isset($_FILES['image']))
{

$file_name = $_FILES['image']['name'];   
$temp_file_location = $_FILES['image']['tmp_name']; 




// Step 1 - Scan for viruses with Cloudmersive Virus Scan

require_once(__DIR__ . '/vendor/autoload.php');

// Configure API key authorization: Apikey
$config = Swagger\Client\Configuration::getDefaultConfiguration()->setApiKey('Apikey', 'API-KEY-HERE');



$apiInstance = new Swagger\Client\Api\ScanApi(
    new GuzzleHttp\Client(  array('verify'          => false)  ),
    $config
);
$input_file = $temp_file_location; // \SplFileObject | Input file to perform the operation on.

$result = $apiInstance->scanFile($input_file);

if (! $result->getCleanResult())
{
    throw new Exception('File contains viruses!');
}

file_put_contents("php://output", "File is clean and safe to upload, proceeding to upload");

// Step 2 - Upload to S3


try
{

		// require 'vendor/autoload.php';

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
catch (Exception $e)
{
    
}

}
?>

<form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">         
	<input type="file" name="image" />
	<input type="submit"/>
</form>      