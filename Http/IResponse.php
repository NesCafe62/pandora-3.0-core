<?php
namespace pandora3\core\Http;

interface IResponse {

	public function send(): void;

	/**
	 * @param string $content
	 */
	public function setContent(string $content): void;

	/**
	 * @param string $content
	 */
	public function addContent(string $content): void;

	/**
	 * @param string $header
	 * @param string $value
	 */
	public function addHeader(string $header, string $value): void;

	/**
	 * @param string $header
	 */
	public function addHeaderRaw(string $header): void;

}