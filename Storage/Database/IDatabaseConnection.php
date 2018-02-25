<?php
namespace pandora3\core\Storage\Database;

interface IDatabaseConnection {

	public function __construct(array $connectionParams);

}