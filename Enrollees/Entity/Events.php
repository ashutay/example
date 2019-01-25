<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: Shutay Alexander
 */

namespace Example\Enrollees\Entity;

use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Entity\Query;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;
use Bitrix\Sale\Internals\BasketTable;

/**
 * None in this project
 */
use App\Entity\ElementPropertyTable;
use Tools\Finder\Iblock;

/**
 * Class Events
 * @package Example\Enrollees\Entity
 */
class Events extends AbstractEntity
{
	/**
	 * @var \Tools\Finder\Helpers\Iblock
	 */
	protected $iblock;
	protected $iblockGroup;

	/**
	 * Seminars constructor.
	 * @throws \Bitrix\Main\LoaderException
	 */
	public function __construct()
	{

		parent::__construct();

		$this->iblock = Iblock::load('events', 'types');
		$this->iblockGroup = Iblock::load('events', 'groups');
	}

	/**
	 * @return array
	 * @throws \Bitrix\Main\ArgumentException
	 * @throws \Bitrix\Main\ObjectException
	 */
	public function get(): array
	{

		return ElementTable::getList(
			[
				'select'  => [
					'COURSE_ID'     => 'ID',
					'COURSE_NAME'   => 'NAME',
					'GROUP_ID'      => 'GROUP.ID',
					'GROUP_NAME'    => 'GROUP.NAME',
					'ORDER_ID'      => 'BASKET.ORDER_ID',
					'TRAINER_EMAIL' => 'USER.EMAIL'
				],
				'filter'  => Query::filter()->where('ACTIVE_TO', '>', new DateTime())->where(
					'GROUP.ACTIVE_TO',
					'>',
					new DateTime()
				)->whereNotNull(
					'ORDER_ID'
				)->where('BASKET.ORDER.CANCELED', false)->whereNotNull('TRAINER_EMAIL'),
				'runtime' => [
					new ReferenceField(
						'SKU', ElementPropertyTable::class, Query\Join::on('this.ID', 'ref.VALUE')->where(
						'ref.IBLOCK_PROPERTY_ID',
						$this->iblockGroup->getPropertyId('CML2_LINK')
					), ['JOIN_TYPE' => 'INNER']
					),
					new ReferenceField(
						'GROUP', ElementTable::class, Query\Join::on('this.SKU.IBLOCK_ELEMENT_ID', 'ref.ID')
					),
					new ReferenceField(
						'BASKET', BasketTable::class, Query\Join::on('this.SKU.IBLOCK_ELEMENT_ID', 'ref.PRODUCT_ID')
					),
					new ReferenceField(
						'PROPERTY_TRAINER',
						ElementPropertyTable::class,
						Query\Join::on('this.ID', 'ref.IBLOCK_ELEMENT_ID')->where(
							'ref.IBLOCK_PROPERTY_ID',
							$this->iblock->getPropertyId('TRAINERS')
						)
					),
					new ReferenceField(
						'USER', UserTable::class, Query\Join::on('this.PROPERTY_TRAINER.VALUE', 'ref.ID')
					)
				]
			]
		)->fetchAll();
	}
}