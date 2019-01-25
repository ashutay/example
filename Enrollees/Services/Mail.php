<?php
/**
 * Created by PhpStorm.
 * User: Shutay Alexander
 */

namespace Example\Enrollees\Services;

use Bitrix\Main\Mail\Event;
use Bitrix\Main\Type\DateTime;
use Example\Enrollees\Driver\DriverInterface;

/**
 * Class Mail
 * @package Example\Enrollees\Services
 */
class Mail
{
	/**
	 * @var DriverInterface
	 */
	protected $driver;

	/**
	 * Mail constructor.
	 *
	 * @param DriverInterface $driver
	 */
	public function __construct(DriverInterface $driver)
	{

		$this->driver = $driver;
	}

	/**
	 * @throws \Bitrix\Main\ObjectException
	 */
	public function run()
	{

		$this->format($this->groups($this->driver->get()));

	}

	/**
	 * @param array $orders
	 *
	 * @return array
	 */
	protected function groups(array $orders): array
	{

		$courses = [];
		foreach ($orders as $order) {
			$courses[$order['COURSE_ID']][$order['GROUP_ID']][] = $order;
		}

		return $courses;
	}

	/**
	 * @return string
	 */
	protected function top()
	{

		return '<tr><th>№</th><th>ФИО</th><th>Email</th><th>Телефон</th><th>Город</th></tr>';
	}

	/**
	 * @param array $courses
	 *
	 * @throws \Bitrix\Main\ObjectException
	 */
	protected function format(array $courses)
	{

		if (empty($courses)) {
			return;
		}

		foreach ($courses as $course) {

			$msg = '<h2>List of registrants. Formed: ' . (new DateTime())->format('H:i:s d.m.Y')
			       . '</h2>';
			$msg .= "<p>Course: № {$course[key($course)][0]['COURSE_ID']}, {$course[key($course)][0]['COURSE_NAME']}</p>";
			$email = '';

			foreach ($course as $group) {

				$msg .= "<p>Group: № {$group[0]['GROUP_ID']}, {$group[0]['GROUP_NAME']}.</p>";
				$msg .= '<table>';
				$msg .= $this->top();

				foreach ($group as $key => $order) {
					$email = $order['TRAINER_EMAIL'];
					$msg .= '<tr>';
					$msg .= '<th>' . ($key + 1) . '</th>';
					$msg .= "<th>{$order['ORDER_PROPERTY']['USER']}</th>";
					$msg .= "<th>{$order['ORDER_PROPERTY']['EMAIL']}</th>";
					$msg .= "<th>{$order['ORDER_PROPERTY']['PHONE']}</th>";
					$msg .= "<th>{$order['ORDER_PROPERTY']['CITY']}</th>";
					$msg .= '</tr>';
				}

				$msg .= '</table>';
			}

			$this->send($msg, $email);
		}
	}

	/**
	 * @param string $msg
	 * @param string $email
	 */
	protected function send(string $msg, string $email)
	{

		Event::send(
			[
				'EVENT_NAME' => 'ENROLLEES',
				'LID'        => 's1',
				'C_FIELDS'   => [
					'MESS'  => $msg,
					'EMAIL' => $email
				],
			]
		);
	}
}