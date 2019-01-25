<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: Shutay Alexander
 */

namespace Example\Enrollees\Entity;

use Bitrix\Main\Loader;

/**
 * Class AbstractEntity
 * @package Example\Enrollees\Entity
 */
abstract class AbstractEntity
{
	/**
	 * AbstractEntity constructor.
	 * @throws \Bitrix\Main\LoaderException
	 */
	public function __construct()
	{

		Loader::includeModule('iblock');
		Loader::includeModule('sale');
	}

	/**
	 * @return array
	 */
	abstract public function get(): array;
}