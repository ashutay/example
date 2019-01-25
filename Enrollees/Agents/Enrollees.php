<?php
/**
 * Created by PhpStorm.
 * User: Shutay Alexander
 */

namespace Example\Enrollees\Agents;

use Example\Enrollees\Driver\Order;
use Example\Enrollees\OrderFinder;
use Example\Enrollees\Services\Mail;

/**
 * None in this project
 */
use Monolog\Registry;

/**
 * Class Enrollees
 * @package Example\Enrollees\Agents
 */
class Enrollees
{
	/**
	 *
	 */
	public static function run()
	{

		try {
			(new Mail(new Order(new OrderFinder())))->run();
		}
		catch (\Exception $exception) {
			Registry::getInstance('example')->error($exception->getMessage() . ' ' . $exception->getTraceAsString());
		}
		finally {
			return __METHOD__ . "();";
		}
	}
}