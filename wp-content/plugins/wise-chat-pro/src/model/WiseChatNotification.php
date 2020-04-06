<?php

/**
 * Wise Chat notification model.
 */
class WiseChatNotification {

	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var string
	 */
	private $action;

	/**
	 * @var string
	 */
	private $frequency;

	/**
	 * @var array
	 */
	private $details;

	/**
	 * WiseChatNotification constructor.
	 */
	public function __construct()
	{
		$this->details = array();
	}

	public function asArray() {
		return array(
			'id' => $this->id,
			'type' => $this->type,
			'action' => $this->action,
			'frequency' => $this->frequency,
			'details' => $this->details,
		);
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * @param string $action
	 */
	public function setAction($action)
	{
		$this->action = $action;
	}

	/**
	 * @return string
	 */
	public function getFrequency()
	{
		return $this->frequency;
	}

	/**
	 * @param string $frequency
	 */
	public function setFrequency($frequency)
	{
		$this->frequency = $frequency;
	}

	/**
	 * @return array
	 */
	public function getDetails()
	{
		return $this->details;
	}

	/**
	 * @param array $details
	 */
	public function setDetails($details)
	{
		$this->details = $details;
	}

}