<?php
namespace pandora\core3\Logger;

interface ILogger {

	/**
	 * @param array $message
	 */
	function log(array $message);

}