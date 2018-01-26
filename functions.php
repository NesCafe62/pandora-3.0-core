<?php

/**
 * Converts path slashes to unix format, working on win platform too.
 * @param string $path
 * @return string
 */
function unixPath(string $path) {
	return str_replace('\\', '/', $path);
}

/**
 * Returns true if $substring is at the beginning of source string.
 * @param string $str
 * @param string $substring
 * @return bool
 */
function startsWith(string $str, string $substring) {
	return (strpos($str, $substring) === 0);
}

/**
 * Returns true if $substring is at the end of source string.
 * @param string $str
 * @param string $substring
 * @return bool
 */
function endsWith(string $str, string $substring) {
	return (strrpos($str, $substring) === strlen($str) - strlen($substring));
}

/**
 * Removes substring from the beginning of the source string, if it is found.
 * @param string $str
 * @param string $substring
 * @return string
 */
function trimLeft(string $str, string $substring) {
	if ($substring !== '') {
		if (startsWith($str, $substring)) $str = substr($str, strlen($substring));
		if ($str === false) $str = '';
	}
	return $str;
}

/**
 * Removes substring from the end of the source string, if it is found.
 * @param string $str
 * @param string $substring
 * @return string
 */
function trimRight(string $str, string $substring) {
	if ($substring !== '') {
		if (endsWith($str, $substring)) $str = substr($str, 0, strlen($str) - strlen($substring));
		if ($str === false) $str = '';
	}
	return $str;
}