<?php
namespace pandora\core3\Debug;

use \Exception;

class CoreException extends Exception {
	
	/**
	 * CoreException constructor.
	 * @param array $params
	 * @param int $code
	 * @param Exception|null $previous
	 */
	public function __construct(array $params, $code = E_WARNING, ?Exception $previous = null) {
        parent::__construct(call_user_func_array(['Debug', 'errorMessage'], $params), $code, $previous);
        Debug::logException($this);
    }

}