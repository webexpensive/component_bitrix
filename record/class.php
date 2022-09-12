<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if(!\Bitrix\Main\Loader::includeModule("iblock")) die();

/**
 * @class RecordPage
 */
class RecordPage extends \CBitrixComponent
{

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
	 * Настройки фильтра по городам
	 */
	protected $filter_city = ["=ACTIVE"=>"Y"];

	/**
	 * @var bool
	 *
	 * Настройки выдачи дублей
	 */
	protected $data_doubling = false;

	/**
	 * @var array
	 *
	 * Список выбираемых свойств пользователя
	 */
	protected $select_user = [
		'ID', 'NAME', 'EMAIL', 'UF_SURNAME', 'UF_NAME', 'UF_PATRONYMIC', 'UF_PHONE', 'UF_AGE'
	];

	/**
	 * @var array
	 *
	 * Настройки фильтра по пользователю
	 */
	protected $filter_user = ['ID' => null];

	/**
	 * @var int
	 *
	 * ID пользователя
	 */
	protected $id_user = 0;

	/**
	 * @var int
	 *
	 * Выбранный пользователем город
	 */
	protected $my_city = 0;

	/**
	 * @var string
	 *
	 * Список городов
	 */
	protected $mas_option_city = '';

	public function executeComponent()
	{
		$this->getUser();

		$this->getUserCity();

		$this->selectCity();

		$this->includeComponentTemplate();

		return $this->arResult;
	}

	/**
	 * Если пользователь авторизован, то возвращаем его данные
	 */
	protected function getUser() : void
	{
		if (!is_null($this->checkAuth())) {

			$this->filter_user = ['ID' => $this->id_user];

			$dbUser = \Bitrix\Main\UserTable::getList([
				'select' => $this->select_user,
				'filter' => $this->filter_user
			]);

			$this->arResult['USER'] = $dbUser->fetch();

		}

	}

	/**
	 * Проверка пользователя на авторизацию
	 */
	protected function checkAuth() : ?int
	{
		global $USER;

		if ($USER->IsAuthorized()) {

			return $this->id_user = $USER->GetID();

		} else {

			return null;

		}

	}

	/**
	 * Определение выбранного города пользователем
	 */
	protected function getUserCity() : void
	{
		$this->my_city = intval($_COOKIE['city']);
	}

	/**
	 * Выбор доступных городов к записи
	 */
	protected function selectCity() : void
	{
		$elements_city = \Bitrix\Iblock\Elements\ElementCityTable::getList([
				'select' => $this->select_city,
				'filter' => $this->filter_city,
				'data_doubling' => $this->data_doubling,
			])->fetchAll();

		foreach ($elements_city as $element) {

			$select = '';

			if ( $element['ID'] == $this->my_city ) $select = ' selected';

			$this->mas_option_city .= '<option value="'.$element['ID'].'"'.$select.'>'.$element['NAME'].'</option>';

		}

		$this->arResult['MAS_CITY'] = $this->mas_option_city;
	}

}