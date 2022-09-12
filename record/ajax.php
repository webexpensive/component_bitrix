<?php require( $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php' );
/**
 * @class RecordGetList
 */
class RecordGetList
{
	/**
	 * @var array
	 *
	 * Данные из формы в json
	 */
	protected $data = [];

	/**
	 * @var array
	 *
	 * Данные из формы
	 */
	protected $post_data = [];

	/**
	 * @var array
	 *
	 * Список выбираемых свойств специалистов
	 */
	protected $select_available_spec = [
		"ID", "IBLOCK_ID", "NAME", "AVAILABLE_SPECIALIST_" => "AVAILABLE_SPECIALIST"
	];

	/**
	 * @var array
	 *
	 * Настройки фильтра по специалистам
	 */
	protected $filter_available_spec = [
		"=ACTIVE"=>"Y", "AVAILABLE_CITY.VALUE"=>null
	];

	/**
	 * @var array
	 *
	 * Список выбираемых специалистов
	 */
	protected $select_specialists_spec = [
		"ID", "IBLOCK_ID","NAME"
	];

	/**
	 * @var array
	 *
	 * Настройки фильтра по доступным специалистам
	 */
	protected $filter_specialists_spec = [
		"=ID"=>null, "=ACTIVE"=>"Y"
	];

	/**
	 * @var array
	 *
	 * Список выбираемых дат
	 */
	protected $select_available_date = [
		"ID", "IBLOCK_ID", "NAME", "AVAILABLE_DATE_" => "AVAILABLE_DATE"
	];

	/**
	 * @var array
	 *
	 * Настройки фильтра по доступным датам
	 */
	protected $filter_available_date = [
		"=ACTIVE"=>"Y",
		"AVAILABLE_CITY.VALUE"=>null,
		"AVAILABLE_SPECIALIST.VALUE"=>null,
	];

	/**
	 * @var array
	 *
	 * Список выбираемых свойств доступного времени
	 */
	protected $select_available_time = [
		"ID", "IBLOCK_ID", "NAME", "AVAILABLE_TIME_" => "AVAILABLE_TIME"
	];

	/**
	 * @var array
	 *
	 * Настройки фильтра по доступному времени
	 */
	protected $filter_available_time = [
		"=ACTIVE"=>"Y",
		"AVAILABLE_CITY.VALUE"=>null,
		"AVAILABLE_SPECIALIST.VALUE"=>null,
		"AVAILABLE_DATE.VALUE"=>null
	];

	/**
	 * @var array
	 *
	 * Список выбираемых свойств времени
	 */
	protected $select_record_time = [
		"ID", "IBLOCK_ID", "NAME", "RECORD_TIME_" => "RECORD_TIME"
	];

	/**
	 * @var array
	 *
	 * Настройки фильтра по времени
	 */
	protected $filter_record_time = [
		"=ACTIVE"=>"Y",
		"RECORD_CITY.VALUE"=>null,
		"RECORD_SPECIALIST.VALUE"=>null,
		"RECORD_DATE.VALUE"=>null,
	];

	/**
	 * @var bool
	 *
	 * Настройки выдачи дублей
	 */
	protected $data_doubling = false;

	public function __construct()
  	{
		if (!\Bitrix\Main\Loader::includeModule("iblock")) $this->exit_record(array('error' => 7));

    	$this->getPostData();
    	$this->getDataList();
		$this->processList();
  	}

  	/**
	 * Получаем данные в json формате из формы
	 */
	protected function getPostData() : void
	{
		$this->data = file_get_contents('php://input');

		if ( empty( $this->data ) ) $this->exit_record(array('error' => 1));

		$this->data = json_decode( $this->data, true );
	}

	/**
	 * Разбираем данные
	 */
	protected function getDataList() : void
	{
		foreach ( $this->data as $value => $key ) {
			$this->post_data[$value] = $key;
		}

		if ( !isset($this->post_data['id_v']) ) $this->exit_record(array('error' => 2));
		$this->post_data['id_v'] = filter_var(trim( $this->post_data['id_v'] ), FILTER_VALIDATE_INT);
		if ( !$this->post_data['id_v'] ) $this->exit_record(array('error' => 2));

		if ( !isset($this->post_data['id_get_city']) ) $this->exit_record(array('error' => 3));
		$this->post_data['id_get_city'] = filter_var(trim( $this->post_data['id_get_city'] ), FILTER_VALIDATE_INT);
		if ( !$this->post_data['id_get_city'] ) $this->exit_record(array('error' => 3));
	}

	/**
	 * Возвращаем ответ пользователю
	 */
	protected function exit_record (array $mas) : void
	{
		echo json_encode($mas);
		die();
	}

	/**
	 * Выбираем обработчик
	 */
	protected function processList () : void
	{
		switch ( $this->post_data['id_v'] ) {
			case 1:
				$this->getListSpecialists();
			break;
			case 2:
				$this->getListDate();
			break;
			case 3:
				$this->getListTime();
			break;
			default:
				$this->exit_record(array('error' => 4));
			break;
		}
	}


	/**
	 * Получаем список специалистов
	 */
	protected function getListSpecialists() : void
	{
		$this->filter_available_spec = [
			"=ACTIVE"=>"Y", "AVAILABLE_CITY.VALUE"=>$this->post_data['id_get_city']
		];

		$elements_avail = \Bitrix\Iblock\Elements\ElementAvailableTable::getList([
				'select' => $this->select_available_spec,
				'filter' => $this->filter_available_spec,
				'data_doubling' => $this->data_doubling,
			])->fetchAll();

		$mas_specialists = [];

		foreach ($elements_avail as $element) $mas_specialists[] = $element['AVAILABLE_SPECIALIST_VALUE'];

		$mas_specialists = array_unique($mas_specialists);
		$mas_option_specialists = '';

		foreach ($mas_specialists as $element_sp) {

			$this->filter_specialists_spec = [
				"=ID"=>$element_sp, "=ACTIVE"=>"Y"
			];

			$elements_spec = \Bitrix\Iblock\Elements\ElementSpecialistsTable::getList([
				'select' => $this->select_specialists_spec,
				'filter' => $this->filter_specialists_spec,
				'data_doubling' => $this->data_doubling,
			])->fetchAll();

			foreach ($elements_spec as $element) $mas_option_specialists .= '<option value="'.$element['ID'].'">'.$element['NAME'].'</option>';

		}

		$this->exit_record(array('error' => 0, 'content' => $mas_option_specialists));
	}

	/**
	 * Получаем список доступных дат
	 */
	protected function getListDate() : void
	{
		if ( !isset($this->post_data['id_get_specialist']) ) 	$this->exit_record(array('error' => 5));
		$this->post_data['id_get_specialist'] = filter_var(trim( $this->post_data['id_get_specialist'] ), FILTER_VALIDATE_INT);
		if ( !$this->post_data['id_get_specialist'] ) $this->exit_record(array('error' => 5));

		$this->filter_available_date = [
				"=ACTIVE"=>"Y",
				"AVAILABLE_CITY.VALUE"=>$this->post_data['id_get_city'],
				"AVAILABLE_SPECIALIST.VALUE"=>$this->post_data['id_get_specialist'],
		];

		$elements_avail = \Bitrix\Iblock\Elements\ElementAvailableTable::getList([
			'select' => $this->select_available_date,
			'filter' => $this->filter_available_date,
			'data_doubling' => $this->data_doubling,
		])->fetchAll();

		$mas_option_date = [];

		foreach ($elements_avail as $element) {

			$new_d = explode(".", $element['AVAILABLE_DATE_VALUE']);

			$mas_option_date[] = $new_d[2].'-'.$new_d[1].'-'.$new_d[0];

		}

		$this->exit_record(array('error' => 0, 'content' => $mas_option_date));
	}

	/**
	 * Получаем список доступного времени
	 */
	protected function getListTime() : void
	{

		if ( !isset($this->post_data['id_get_date']) ) 	$this->exit_record(array('error' => 6));
		$this->post_data['id_get_date'] = filter_var(trim( $this->post_data['id_get_date'] ), FILTER_VALIDATE_REGEXP, array("options" => array("regexp"=>"/^(0[1-9]|[12][0-9]|3[01])[\.](0[1-9]|1[012])[\.](19|20)\d\d$/")));
		if ( !$this->post_data['id_get_date'] ) $this->exit_record(array('error' => 6));

		$this->filter_available_time = [
				"=ACTIVE"=>"Y",
				"AVAILABLE_CITY.VALUE"=>$this->post_data['id_get_city'],
				"AVAILABLE_SPECIALIST.VALUE"=>$this->post_data['id_get_specialist'],
				"AVAILABLE_DATE.VALUE"=>$this->post_data['id_get_date']
			];

		$elements_avail = \Bitrix\Iblock\Elements\ElementAvailableTable::getList([
			'select' => $this->select_available_time,
			'filter' => $this->filter_available_time,
			'data_doubling' => $this->data_doubling,
		])->fetchAll();

		$this->filter_record_time = [
				"=ACTIVE"=>"Y",
				"RECORD_CITY.VALUE"=>$this->post_data['id_get_city'],
				"RECORD_SPECIALIST.VALUE"=>$this->post_data['id_get_specialist'],
				"RECORD_DATE.VALUE"=>$this->post_data['id_get_date']
			];

		$elements_record = \Bitrix\Iblock\Elements\ElementRecordTable::getList([
			'select' => $this->select_record_time,
			'filter' => $this->filter_record_time,
			'data_doubling' => $this->data_doubling,
		])->fetchAll();

		$mas_record_time = [];

		foreach ($elements_record as $element) $mas_record_time[] = $element['RECORD_TIME_VALUE'];

		$mas_option_time = [];

		foreach ($elements_avail as $element) {

			$element['AVAILABLE_TIME_VALUE'] = unserialize( $element['AVAILABLE_TIME_VALUE'] );

			$element['AVAILABLE_TIME_VALUE'] = json_decode( $element['AVAILABLE_TIME_VALUE']['TEXT'], true );

			foreach ($element['AVAILABLE_TIME_VALUE'] as $time) {
				if ( !in_array( $time, $mas_record_time ) ) $mas_option_time[] = $time;
			}

		}

		$this->exit_record(array('error' => 0, 'content' => $mas_option_time));
	}


}

new RecordGetList();