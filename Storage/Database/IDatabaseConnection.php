<?php
namespace pandora\core3\Storage\Database;

interface IDatabaseConnection {

	public function __construct(array $connectionParams);

}