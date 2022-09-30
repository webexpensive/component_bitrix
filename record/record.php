<?php require( $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php' );
/**
 * @class RecordPost
 */
class RecordPost
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
	 * Данные по выбранному городу
	 */
	protected $element_city = [];

	/**
	 * @var int
	 *
	 * ID пользователя
	 */
	protected $id_user = 0;

	/**
	 * @var array
	 *
	 * Список выбираемых свойств города
	 */
	protected $select_city = [
		'ID', 'IBLOCK_ID', 'NAME'
	];

	/**
	 * @var array
	 *
	 * Настройки фильтра по городу
	 */
	protected $filter_city = ['ID' => null, "=ACTIVE"=>"Y"];

	/**
	 * @var array
	 *
	 * Список выбираемых свойств записей
	 */
	protected $select_record = [
		"ID", "IBLOCK_ID","NAME", "RECORD_TIME_" => "RECORD_TIME"
	];

	/**
	 * @var array
	 *
	 * Настройки фильтра по городу
	 */
	protected $filter_record = [
			"=ACTIVE"=>"Y",
			"RECORD_CITY.VALUE"=>null,
			"RECORD_SPECIALIST.VALUE"=>null,
			"RECORD_DATE.VALUE"=>null
	];

	/**
	 * @var array
	 *
	 * Данные по выбранному специалисту
	 */
	protected $element_specialist = [];

	/**
	 * @var array
	 *
	 * Список выбираемых свойств специалиста
	 */
	protected $select_specialist = [
		"ID", "IBLOCK_ID","NAME"
	];

	/**
	 * @var array
	 *
	 * Настройки фильтра по специалистам
	 */
	protected $filter_specialist = [
			"=ID"=>null,
			"=ACTIVE"=>"Y"
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
		$this->getCity();
		$this->getSpecialist();
		$this->getRecords();
		$this->upUser();
		$this->addRecord();
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

		if ( !isset($this->post_data['record_city']) ) $this->exit_record(array('error' => 3));
		$this->post_data['record_city'] = filter_var(trim( $this->post_data['record_city'] ), FILTER_VALIDATE_INT);
		if ( !$this->post_data['record_city'] ) $this->exit_record(array('error' => 3));

		if ( !isset($this->post_data['record_specialist']) ) $this->exit_record(array('error' => 5));
		$this->post_data['record_specialist'] = filter_var(trim( $this->post_data['record_specialist'] ), FILTER_VALIDATE_INT);
		if ( !$this->post_data['record_specialist'] ) $this->exit_record(array('error' => 5));

		if ( !isset($this->post_data['record_date']) ) 	$this->exit_record(array('error' => 6));
		$this->post_data['record_date'] = filter_var(trim( $this->post_data['record_date'] ), FILTER_VALIDATE_REGEXP, array("options" => array("regexp"=>"/^(0[1-9]|[12][0-9]|3[01])[\.](0[1-9]|1[012])[\.](19|20)\d\d$/")));
		if ( !$this->post_data['record_date'] ) $this->exit_record(array('error' => 6));

		if ( !isset($this->post_data['record_time']) ) $this->exit_record(array('error' => 5));

		if ( !isset($this->post_data['record_surname']) ) $this->exit_record(array('error' => 8));
		$this->post_data['record_surname'] = filter_var(trim( $this->post_data['record_surname'] ), FILTER_VALIDATE_REGEXP, array("options" => array("regexp"=>"/^[а-яё\s\-]+$/ui")));
		if ( !$this->post_data['record_surname'] ) $this->exit_record(array('error' => 8));

		if ( !isset($this->post_data['record_name']) ) $this->exit_record(array('error' => 9));
		$this->post_data['record_name'] = filter_var(trim( $this->post_data['record_name'] ), FILTER_VALIDATE_REGEXP, array("options" => array("regexp"=>"/^[а-яё\s\-]+$/ui")));
		if ( !$this->post_data['record_name'] ) $this->exit_record(array('error' => 9));

		if ( !isset($this->post_data['record_patronymic']) ) $this->exit_record(array('error' => 10));
		$this->post_data['record_patronymic'] = filter_var(trim( $this->post_data['record_patronymic'] ), FILTER_VALIDATE_REGEXP, array("options" => array("regexp"=>"/^[а-яё\s\-]+$/ui")));
		if ( !$this->post_data['record_patronymic'] ) $this->exit_record(array('error' => 10));

		if ( !isset($this->post_data['record_age']) ) $this->exit_record(array('error' => 11));
		$this->post_data['record_age'] = filter_var(trim( $this->post_data['record_age'] ), FILTER_VALIDATE_INT);
		if ( !$this->post_data['record_age'] ) $this->exit_record(array('error' => 11));

		if ( !isset($this->post_data['record_phone']) ) $this->exit_record(array('error' => 12));
		$this->post_data['record_phone'] = filter_var(trim( $this->post_data['record_phone'] ), FILTER_VALIDATE_REGEXP, array("options" => array("regexp"=>"/^[0-9\+\(\)\-]+$/ui")));
		if ( !$this->post_data['record_phone'] ) $this->exit_record(array('error' => 12));

		$this->post_data['record_phone'] = str_replace(array('+','(',')','-'), array(''), $this->post_data['record_phone']);
		$this->post_data['record_phone'] = trim( $this->post_data['record_phone'] );
		$this->post_data['record_phone'] = mb_substr($this->post_data['record_phone'], 1, 10, 'UTF8');

		if ( !isset($this->post_data['record_email']) ) $this->exit_record(array('error' => 13));
		$this->post_data['record_email'] = filter_var(trim( $this->post_data['record_email'] ), FILTER_VALIDATE_EMAIL);
		if ( !$this->post_data['record_email'] ) $this->exit_record(array('error' => 13));
	}

	/**
	 * Получение наименования города
	 */
	protected function getCity() : void
	{
		$this->filter_city = [
			"=ID"=>$this->post_data['record_city'],
			"=ACTIVE"=>"Y"
		];

		$this->element_city = \Bitrix\Iblock\Elements\ElementCityTable::getList([
			'select' => $this->select_city,
			'filter' => $this->filter_city,
			'data_doubling' => $this->data_doubling,
		])->fetch();
	}

	/**
	 * Получение данных специалиста
	 */
	protected function getSpecialist() : void
	{
		$this->filter_specialist = [
			"=ID"=>$this->post_data['record_specialist'],
			"=ACTIVE"=>"Y"
		];

		$this->element_specialist = \Bitrix\Iblock\Elements\ElementSpecialistsTable::getList([
			'select' => $this->select_specialist,
			'filter' => $this->filter_specialist,
			'data_doubling' => $this->data_doubling,
		])->fetch();
	}

	/**
	 * Проверка, что выбранное время свободно
	 */
	protected function getRecords() : void
	{
		$this->filter_record = [
			"=ACTIVE"=>"Y",
			"RECORD_CITY.VALUE"=>$this->post_data['record_city'],
			"RECORD_SPECIALIST.VALUE"=>$this->post_data['record_specialist'],
			"RECORD_DATE.VALUE"=>$this->post_data['record_date']
		];

		$elements_record = \Bitrix\Iblock\Elements\ElementRecordTable::getList([
			'select' => $this->select_record,
			'filter' => $this->filter_record,
			'data_doubling' => $this->data_doubling,
		])->fetchAll();

		$mas_record_time = [];

		foreach ($elements_record as $element) {
			$mas_record_time[] = $element['RECORD_TIME_VALUE'];
		}

		if ( in_array( $this->post_data['record_time'], $mas_record_time ) ) $this->exit_record(array('error' => 15));
	}

	/**
	 * Обновление данных пользователя
	 */
	protected function upUser() : void
	{
		global $USER;

		$this->getIDUser();

		if ( $this->id_user == 0 ) {
			$password = $this->generatePassword(12);
			$arResult = $USER->Register($this->post_data['record_email'], $this->post_data['record_name'], $this->post_data['record_surname'], $password, $password, $this->post_data['record_email']);
			$this->id_user = $USER->GetID();
		}

		$user_l = new CUser;
		$fields = Array(
			"EMAIL"             => $this->post_data['record_email'],
			"UF_SURNAME"        => $this->post_data['record_surname'],
			"UF_NAME"           => $this->post_data['record_name'],
			"UF_PATRONYMIC"     => $this->post_data['record_patronymic'],
			"UF_PHONE"          => $this->post_data['record_phone'],
			"UF_AGE"          	=> $this->post_data['record_age']
		);
		$user_l->Update($this->id_user, $fields);
	}

	protected function getIDUser() : void
	{
		global $USER;

		$filter = Array("=EMAIL" => $this->post_data['record_email']);
		$sql = CUser::GetList(($by="id"), ($order="desc"), $filter);
		if($sql->NavNext(true, "f_")) $this->id_user = $f_ID;
	}

	/**
	 * Запись в инфоблок
	 */
	protected function addRecord() : void
	{
		$el = new CIBlockElement;

		$PROP = [];
		$PROP[5] = $this->post_data['record_city'];
		$PROP[6] = $this->post_data['record_specialist'];
		$PROP[7] = $this->post_data['record_date'];
		$PROP[8] = $this->post_data['record_time'];
		$PROP[9] = $this->id_user;
		$PROP[18] = $this->post_data['record_surname'];
		$PROP[19] = $this->post_data['record_name'];
		$PROP[20] = $this->post_data['record_patronymic'];
		$PROP[21] = $this->post_data['record_age'];
		$PROP[22] = $this->post_data['record_email'];
		$PROP[23] = $this->post_data['record_phone'];

		$arLoadProductArray = Array(
			"MODIFIED_BY"    => $this->id_user,
			"IBLOCK_SECTION_ID" => false,
			"IBLOCK_ID"      => 7,
			"PROPERTY_VALUES"=> $PROP,
			"NAME"           => $this->post_data['record_surname']. ' ' . $this->post_data['record_name'] . ' ' . $this->post_data['record_patronymic'] . ' - ' . $this->element_specialist['NAME'].' в '.$this->element_city['NAME']." на ".$this->post_data['record_date'] . ' '.$this->post_data['record_time'],
			"ACTIVE"         => "Y"
		);

		if($PRODUCT_ID = $el->Add($arLoadProductArray)) {

			$this->sendLetter();
			$this->exit_record(array('error' => 0));
		}
	}

	/**
	 * Отправка письма на почту
	 */
	protected function sendLetter(): void
	{
		\Bitrix\Main\Mail\Event::sendImmediate(array(
			"EVENT_NAME" => "SEND_RECORD_USER",
			"LID" => "s1",
			"C_FIELDS" => array(
				"EMAIL" => $this->post_data['record_email'],
				"CITY" => $this->element_city['NAME'],
				"DATE" => $this->post_data['record_date'],
				"TIME" => $this->post_data['record_time'],
				"SPECIALIST" => $this->element_specialist['NAME'],
				"FIO" => $this->post_data['record_surname']. ' ' . $this->post_data['record_name'] . ' ' . $this->post_data['record_patronymic'],
				"AGE" => $this->post_data['record_age'],
				"PHONE" => $this->post_data['record_phone']
			),
		));
	}

	/**
	 * Генерация пароля
	 */
	protected function generatePassword(int $length = 8): string
	{
		$password = '';
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$limit = strlen($characters) - 1;
		for ($i = 0; $i < $length; $i++) {
			$password .= $characters[rand(0, $limit)];
		}
		return $password;
	}

	/**
	 * Возвращаем ответ пользователю
	 */
	protected function exit_record(array $mas) : void
	{
		echo json_encode($mas);
		die();
	}
}

new RecordPost();
