<?php
//namespace ZipStream\Exception;
require_once ("Exception.php");

/**
 * This Exception gets invoked if a file wasn't found
 * 
 * @author Jonatan M�nnchen <jonatan@maennchen.ch>
 * @copyright Copyright (c) 2014, Jonatan M�nnchen
 */
class FileNotFoundException extends Exception {
	/**
	 * Constructor of the Exception
	 * 
	 * @param String $path - The path which wasn't found
	 */
	public function __construct($path) {
		parent::__construct("Ths file with the path $path wasn't found.");
	}
}