<?php
//namespace ZipStream\Exception;
//use ZipStream\Exception;
require_once ("Exception.php");

/**
 * This Exception gets invoked if an invalid parameter gets passed on as option
 * 
 * @author Jonatan M�nnchen <jonatan@maennchen.ch>
 * @copyright Copyright (c) 2014, Jonatan M�nnchen
 */
class InvalidOptionException extends Exception {
	/**
	 * Constructor of the Exception
	 * 
	 * @param String $optionName - The name of the Option
	 * @param string[] $expectedValues - All possible Values
	 * @param String $givenValue
	 */
	public function __construct($optionName, $expectedValues = array(), $givenValue) {
		parent::__construct("Invalid Option $optionName. EXPECTED: " . implode(", ", $expectedValues) . " GIVEN: $givenValue");
	}
}