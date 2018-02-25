<?php
namespace pandora3\core\Http;

interface IRequest {

	/**
	 * @param string $param
	 * @return string|null
	 */
	public function get(string $param);

	/**
	 * @param string $param
	 * @return string|null
	 */
	public function post(string $param);

	/**
	 * @param string $param
	 * @return array|null
	 */
	public function file(string $param);

}