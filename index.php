<?php
  /*
   * //TODO: license, disclaimer, etc
   */

/****************************************************
 *
 *	Configuration section
 *
 ****************************************************/

 //can be relative too
	$basePaths[] = '..';
    $keyFile = 'public.key';
   
   
   
/****************************************************
 *
 *	Classes
 *
 ****************************************************/
   
   
interface Runnable
{
    // method declaration
    public function run();
}   
   

class GetFilesRunnable implements Runnable
{
	private $files = array();
	
	function __construct($fileListParam) {
        //parent::__construct();
	   
		$this->files = $fileListParam;
		require_once("ZipStream.php");

    }
	
    // method declaration
    public function run() {
		$zip = new ZipStream();

		foreach($this->files as $file) {
			$data = file_get_contents($file);
			$zip->addFile($file, $data);
		}
		
		$zip->finish();
	}
}   

class FileListRunnable implements Runnable
{
	private $timestamp = 0;
	
	function __construct($timestampParam) {
       //parent::__construct();
	   require_once("ZipStream.php");
	   $this->timestamp = $timestampParam;
	   
    }
	
    // method declaration
    public function run() {
		//TODO ugly global here - find better way without bloat
		global $basePaths;
		$arrayOfFileNames = array();
		foreach($basePaths as $basePath) {
			//listInDir($arrayOfFileNames, dirname(__FILE__), create_function('$fileInfo', 'return $fileInfo->getMTime()>'.$this->timestamp.';'));
			listInDir($arrayOfFileNames, realpath($basePath), create_function('$fileInfo', 'return $fileInfo->getMTime()>'.$this->timestamp.';'));
		}

		$zip = new ZipStream();
		$zip->addFile('filelist.txt', implode($arrayOfFileNames));
		$zip->finish();
	}
}   
   


/****************************************************
 *
 *	Utility methods
 *
 ****************************************************/

 
function startsWith($haystack, $needle)
{
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

/**
 * Handles authentication: HTTP authentication.
 */ 
function authenticate() {
	//TODO: digest?
	
	
	return false;
}

//recursive
function listInDir(&$addTo, $dirName, $filterFunction = null)  {

	$dir = new DirectoryIterator($dirName);

	foreach ($dir as $fileinfo) {
		if (!$fileinfo->isDot()) {
			if($fileinfo->isDir()) {
				listInDir($addTo, $fileinfo->getPathname(), $filterFunction);
			}
			else {
				if($filterFunction && $filterFunction($fileinfo)) {
					$addTo[] = getFileDataString($fileinfo);
				}
			}
		}
	}
}

function getFileDataString($fileInfo) {
	return $fileInfo->getPathname()."\t".$fileInfo->getSize()."\t".$fileInfo->getMTime()."\n";
}

//listInDir(dirname(__FILE__));

function getTimeStamp() {
	$toReturn = 0;
	$raw=trim($_REQUEST['timestamp']) ;
	// 9223372036854775807 -> 19 char long
	// 64 bits should be enough for almost everyone, except the Lone Wanderer from Fallout 3 http://fallout.wikia.com/wiki/Timeline#2259
	if(strlen($raw)<=19)  {
		$toReturn=intval($raw);
	}
	return $toReturn;
}

function isInBasePaths($fileName) {
	global $basePaths;
	foreach($basePaths as $basePath) {
		if(startsWith(realpath($fileName), realPath($basePath))) {
			return true;
		}
	}
	return false;
}


function getActivity() {
	return $_REQUEST['activity'];
}

function encryptAndOutputData($data, $fileName) {
	global $keyFile;
	// fetch public keys for our recipients, and ready them
	$fp = fopen($keyFile, "r");
	$cert = fread($fp, 8192);
	fclose($fp);
	$pk1 = openssl_get_publickey($cert);

	// seal message, only owners of $pk1 and $pk2 can decrypt $sealed with keys
	// $ekeys[0] and $ekeys[1] respectively.
	openssl_seal($data, $sealed, $ekeys, array($pk1));

	//do headers
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: public");
	header("Content-Description: File Transfer");
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=\"".$fileName."\"");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".strlen($sealed) );
	header("X-envelope-key: ".base64_encode($ekeys[0]) );
	
	echo $sealed;
	//echo $data;

	// free the keys from memory
	openssl_free_key($pk1);	
	
}


function doListFiles($timeStamp) {
	$callbackObject = new FileListRunnable($timeStamp);
	doRespond($callbackObject, "downloaded.zip");
}


function doGetFiles($fileList) {
	$callbackObject = new GetFilesRunnable($fileList);
	doRespond($callbackObject, "downloaded.zip");
}

function doRespond(Runnable $callbackObject, $fileName) {
	
	//Buffer
	ob_start();

	
	$callbackObject->run();
	
	$data = ob_get_contents();

	ob_end_clean();
	encryptAndOutputData($data, $fileName);
}


function getFileList() {

	$fileList = $_REQUEST['fileNames'];
	
	
	$toReturn = array();
	//verify and check all of them
	foreach($fileList as $fileName) {
		//no parent dir!
		if(strpos($fileName, '..') !== false) {
			//reject!
			die;
		}
			
		if(!isInBasePaths($fileName)) {
			die;
		}
		
		if(!in_array($fileName, $toReturn)) {
			$toReturn[]=$fileName;
		}
		
	}



	return $toReturn;
}

/****************************************************
 *
 *	Beginning operation
 *
 ****************************************************/


 
authenticate();

switch(getActivity()) {
	case "listFiles":
		doListFiles(getTimeStamp());
	break;
	case "getFiles":
		doGetFiles(getFileList());
	break;
}




?>