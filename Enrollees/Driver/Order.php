<?php
/**
 * Created by PhpStorm.
 * User: Shutay Alexander
 */

namespace Example\Enrollees\Driver;

use Example\Enrollees\OrderFinder;

/**
 * None in this project
 */
use Tools\Finder\OrderProperty;

/**
 * Class Order
 * @package Example\Enrollees\Driver
 */
class Order implements DriverInterface
{
	/**
	 * @var OrderFinder
	 */
	protected $orderFinder;

	/**
	 * Order constructor.
	 *
	 * @param OrderFinder $orderFinder
	 */
	public function __construct(OrderFinder $orderFinder)
	{

		$this->orderFinder = $orderFinder;
	}

	/**
	 * @return array
	 * @throws \Bitrix\Main\ArgumentNullException
	 * @throws \Bitrix\Main\NotImplementedException
	 */
	public function get(): array
	{

		$entitiesCollection = $this->orderFinder->get();
		$orders = [];
		foreach ($entitiesCollection as $entity) {

			if (!\method_exists($entity, 'get')) {
				continue;
			}

			foreach ($entity->get() as $order) {
				if (empty($order['ORDER_ID'])) {
					continue;
				}

				$orders[$order['ORDER_ID']] = $order;
				$orders[$order['ORDER_ID']]['ORDER_PROPERTY'] = $this->orderProperty($order['ORDER_ID']);
			}

		}

		return $orders;
	}

	/**
	 * @param int $orderId
	 *
	 * @return array|null
	 * @throws \Bitrix\Main\ArgumentNullException
	 * @throws \Bitrix\Main\NotImplementedException
	 */
	protected function orderProperty(int $orderId): ?array
	{

		$orderProperty = [];

		$order = \Bitrix\Sale\Order::load($orderId);

		if ($order === null) {
			return null;
		}

		$properties = [
			'USER',
			'CITY',
			'EMAIL',
			'PHONE'
		];

		foreach ($properties as $property) {
			$orderProperty[$property] = $this->propertyValue($order, $property);
		}

		return $orderProperty;
	}

	/**
	 * @param \Bitrix\Sale\Order $order
	 * @param                    $property
	 *
	 * @return string|null
	 */
	protected function propertyValue(\Bitrix\Sale\Order $order, $property): ?string
	{

		$propertyId = OrderProperty::getId($order->getPersonTypeId(), $property);

		if (!$propertyId) {
			return null;
		}

		$property = $order->getPropertyCollection()->getItemByOrderPropertyId($propertyId);

		if ($property !== null) {
			return (string)$property->getValue();
		}

		return null;
	}
}