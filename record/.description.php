<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
	"NAME" => Loc::getMessage("ACU_RECORD_COMPONENT"),
	"DESCRIPTION" => Loc::getMessage("ACU_RECORD_COMPONENT_DESCRIPTION"),
	"ICON" => "/images/record.png",
	"COMPLEX" => "N",
	"PATH" => array(
		"ID" => "record",
		"NAME" => Loc::getMessage("ACU_RECORD_PAGE_COMPONENT")
	),
);