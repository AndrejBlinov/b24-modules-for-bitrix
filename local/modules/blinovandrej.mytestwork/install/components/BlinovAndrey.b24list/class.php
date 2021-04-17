<?

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
use \Bitrix\Iblock;
use Bitrix\Main\Application;

// Подключаем файл локализации
Loc::loadMessages(__FILE__);

// Подключаем модуль работы с инфоблоками
if (!\Bitrix\Main\Loader::includeModule('iblock'))
{
	ShowError(Loc::getMessage('IBLOCK_MODULE_NOT_INSTALLED'));
	return;
}

if(!isset($arParams["CACHE_TIME"])){
	$arParams["CACHE_TIME"] = 36000000;
}


class b24workComponent extends CBitrixComponent {

	public function executeComponent(){
		$this->arResult=$this->getData();
		$this->includeComponentTemplate();
	}

	private function getList() {

		$arSort = array();
		$arFilter = Array("IBLOCK_ID"=>$this->arParams["IBLOCK_ID"], "ACTIVE"=>"Y");

		$arSelect = Array(
			"ID", 
			"*",
		);  

		$res = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);

        $arItems = array();

        while ($arItem = $res->fetch()) {
			$arItems[] = $arItem ;
        }
        return $arItems;

	}

	public function getData(){

		$cntIBLOCK_List = $arParams["IBLOCK_ID"];
		$cache = new CPHPCache();
		$cache_time = $arParams["CACHE_TIME"];
		$cache_id = 'arIBlockListID'.$cntIBLOCK_List;
		$cache_path = '/arIBlockListID/';
		if ($cache_time > 0 && $cache->InitCache($cache_time, $cache_id, $cache_path))
		{
		   $res = $cache->GetVars();
		   if (is_array($res["arIBlockListID"]) && (count($res["arIBlockListID"]) > 0))
			  $arIBlockListID = $res["arIBlockListID"];
		}
		if (!is_array($arIBlockListID))
		{
		   $arIBlockListID = $this->getList();
		   //////////// end cache /////////
		   if ($cache_time > 0)
		   {
				 $cache->StartDataCache($cache_time, $cache_id, $cache_path);
				 $cache->EndDataCache(array("arIBlockListID"=>$arIBlockListID));
		   }
		}

		return $arIBlockListID;
	}
}
?>
