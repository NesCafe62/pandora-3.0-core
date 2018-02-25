<?php
namespace pandora3\core\Logger;

interface ILogger {

	/**
	 * @param array $message
	 */
	function log(array $message): void;

}