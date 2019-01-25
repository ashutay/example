<?php
/**
 * Created by PhpStorm.
 * User: Shutay Alexander
 */

namespace Example\Enrollees\Driver;

/**
 * Interface DriverInterface
 * @package Example\Enrollees\Driver
 */
interface DriverInterface
{
	/**
	 * @return array|null
	 */
	public function get(): ?array;
}