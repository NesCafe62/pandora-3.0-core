<?php
namespace pandora3\core\DI\Exceptions;

use Exception;

class DIKeyNotFoundException extends DIException {
	
	/**
	 * DIKeyNotFoundException constructor.
	 * @param string $key
	 * @param int $code
	 * @param Exception|null $previous
	 */
	public function __construct(string $key, $code = E_WARNING, ?Exception $previous = null) {
		parent::__construct(['DI_DEPENDENCY_KEY_NOT_FOUND', $key], $code, $previous);
	}
	
}