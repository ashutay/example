<?php
/**
 * Created by PhpStorm.
 * User: Shutay Alexander
 */

namespace Example\Enrollees;

/**
 * Class OrderFinder
 * @package Example\Enrollees
 */
class OrderFinder
{
	/**
	 *
	 */
	protected const ENTITIES = [
		'Events'
	];

	/**
	 * @return array
	 */
	public function get(): array
	{

		$entitiesCollection = [];
		foreach (self::ENTITIES as $class) {
			$sClass = __NAMESPACE__ . '\\Entity\\' . $class;

			if (\class_exists($sClass)) {
				$entitiesCollection[] = new $sClass();
			}
		}

		return $entitiesCollection;
	}
}