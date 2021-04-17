<?
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;
//use blinovandrej_mytestwork/ as TW ;

Loc::loadMessages(__FILE__);

Class blinovandrej_mytestwork extends CModule
{
	var $MODULE_ID = "blinovandrej.mytestwork";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $IBLOCK_TYPE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";
	var $NAME_MODULE = "BLINOVANDREJ_MYTESTWORK" ;

    function __construct()
    {
        $arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->IBLOCK_TYPE = "testworkforbitrix"; 

		$this->MODULE_NAME = Loc::getMessage($this->NAME_MODULE."_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = Loc::getMessage($this->NAME_MODULE."_INSTALL_DESCRIPTION");
		$this->PARTNER_NAME = Loc::getMessage($this->NAME_MODULE."_DEV_NAME");
    }

	function DoInstall()
	{
		global $APPLICATION;

		$url = "https://uttest1.bitrix24.ru/rest/1/demfzvh9s1hbm9lg/";
		$this->SetOptions("webhkUrl",$url);

		if ($this->isVersionD7())
		{
			$this->InstallFiles() ;
			$this->InstallDB() ;
			$result = $this->CreateIblocks();
			if ($result)
			{
				RegisterModule($this->MODULE_ID);
				Loc::getMessage($this->NAME_MODULE."_FORM_INSTALL_TITLE");
				$this->getAndAddStatusList();
			}
			else{
				$APPLICATION->ThrowException(Loc::getMessage($this->NAME_MODULE."_INSTALL_ERROR_IBLOCK_ERROR_INSTAL"));
			}
		}	
		else 
		{
			$APPLICATION->ThrowException(Loc::getMessage($this->NAME_MODULE."_INSTALL_ERROR"));
		}

		$APPLICATION->IncludeAdminFile(Loc::getMessage($this->NAME_MODULE."_FORM_INSTALL_TITLE"), $this->GetPath()."/install/step.php");

	}

	function SetOptions($key,$value)
	{
		COption::SetOptionString($this->MODULE_ID, $key, $value);
	}

	function GetPath() {

		return $_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID ;
	}

	function DoUninstall()
	{
		global $APPLICATION;

		$contect = Application::getinstance()->getContext() ;
		$request = $contect->getRequest();

		if ($request["step"]<2)
		{
			$APPLICATION->IncludeAdminFile(GetMessage($this->NAME_MODULE."_FORM_UNSTALL_TITLE"), $this->GetPath()."/install/unstep1.php");
		}
		elseif($request["step"]==2)
		{
			if ($request["saveData"]!='Y')
			{
				$this->UnInstallDB() ;
			}

			\Bitrix\Main\ModuleManager::UnRegisterModule($this->MODULE_ID);
			$APPLICATION->IncludeAdminFile(GetMessage($this->NAME_MODULE."_FORM_UNSTALL_TITLE"), $this->GetPath()."/install/unstep.php");
		}


	}

	//установленый компонент
	function UnInstallFiles()
	{
		return true;
	}
	//заполняем справочник статусов
	function getAndAddStatusList(){
		if(Cmodule::includeModule('blinovandrej.mytestwork')){
			$method = 'crm.status.list';
			$params = array("ENTITY_ID"=>"DEAL_STAGE");
			$result = blinovandrej\mytestwork\bwork::GetData($method,$params);
			// blinovandrej\mytestwork\
			//добавляем элементы в инфоблок/ заполняем справочник статусов
			foreach($result['result'] as $item)
			{
				$res = $this->addInIblock($item);
			}
		}
	}



	function InstallFiles()
	{
		return true;
	}

	function InstallDB()
	{
		return true;
	}

	function isVersionD7()
	{
		return true;
	}

	function AddIblockType($arFieldsIBT){
		global $DB;
		CModule::IncludeModule("iblock");
	
		$iblockType = $arFieldsIBT["ID"];
	
		// Работа с типом инфоблока
		// проверяем наличие нужного типа инфоблока
		$db_iblock_type = CIBlockType::GetList(Array("SORT" => "ASC"), Array("ID" => $iblockType));
		// если его нет - создаём
		if (!$ar_iblock_type = $db_iblock_type->Fetch()){
			$obBlocktype = new CIBlockType;
			$DB->StartTransaction();
			$resIBT = $obBlocktype->Add($arFieldsIBT);
			if (!$resIBT){
				$DB->Rollback();
				echo 'Error: '.$obBlocktype->LAST_ERROR.'';
				die();
			}else{
				$DB->Commit();
			}
		}else{
			return false;
		}
	
		return $iblockType;
	}
	// функция добавления инфоблока
	function AddIblock($arFieldsIB){
		CModule::IncludeModule("iblock");
	
		$iblockCode = $arFieldsIB["CODE"];
		$iblockType = $arFieldsIB["TYPE"];
	
		$ib = new CIBlock;
	
		// проверка на наличие создание/обновление
		$resIBE = CIBlock::GetList(Array(), Array('TYPE' => $iblockType, "CODE" => $iblockCode));
		if ($ar_resIBE = $resIBE->Fetch()){
			return false; // желаемый код занят
		}else{
			$ID = $ib->Add($arFieldsIB);
			$iblockID = $ID;
		}
	
		return $iblockID;
	}

	private function CreateIblocks(){
		// для типа инфоблоков
		$arFieldsForType = Array(
			'ID' => $this->IBLOCK_TYPE,
			'SECTIONS' => 'Y',
			'IN_RSS' => 'N',
			'SORT' => 500,
			'LANG' => Array(
				'en' => Array(
					'NAME' => 'testworkforbitrix',
				),
				'ru' => Array(
					'NAME' => "Тестовая работа для Б24",
				)
			)
		);

		// если создали тип инфоблока, создаём инфоблоки
		if ($this->AddIblockType($arFieldsForType)){

			$arFieldsForIblock = Array(
				"ACTIVE" => "Y",
				"NAME" => "Справочник статусов",
				"CODE" => "statusB24List",
				"IBLOCK_TYPE_ID" => $arFieldsForType["ID"],
				"SITE_ID" => "s1",
				"GROUP_ID" => Array("2" => "R"),
				"FIELDS" => Array(
					"CODE" => Array(
						"IS_REQUIRED" => "Y",
						"DEFAULT_VALUE" => Array(
							"TRANS_CASE" => "L",
							"UNIQUE" => "Y",
							"TRANSLITERATION" => "Y",
							"TRANS_SPACE" => "-",
							"TRANS_OTHER" => "-"
						)
					)
				)
			);

			// если создали инфоблок, можем создать ему свойства
			if ($iblockID = $this->AddIblock($arFieldsForIblock)){
				$this->SetOptions("iblockCreated",$iblockID);
				return true;
			}else{
				CAdminMessage::ShowMessage(Array(
					"TYPE" => "ERROR",
					"MESSAGE" => GetMessage("VTEST_IBLOCK_NOT_INSTALLED"),
					"DETAILS" => "",
					"HTML" => true
				));
			}
		}else{
			CAdminMessage::ShowMessage(Array(
				"TYPE" => "ERROR",
				"MESSAGE" => GetMessage("VTEST_IBLOCK_TYPE_NOT_INSTALLED"),
				"DETAILS" => "",
				"HTML" => true
			));
		}
	}

	function addInIblock($data) {
		$PR = array();
		$PR = $this->SetProperty($data);
		$el = new CIBlockElement;
		$arLoadProductArray = Array(
			// "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
			//"IBLOCK_SECTION" => array($this->$SectionID_forStatusList),          // элемент лежит в корне раздела
			"PROPERTY_VALUES"=> $PR,
			"NAME"           => !empty($data['NAME_INIT']) ? $data['NAME_INIT'] : $data['STATUS_ID'] ,
			"ACTIVE"         => "Y",            // активен
			"IBLOCK_ID"		=> $res = COption::GetOptionString("blinovandrej.mytestwork", "iblockCreated"),
			"CODE"			=> $data['STATUS_ID'],
			"PREVIEW_TEXT" => $data['ID_B24'] ,
		);
		$ELEMENT_ID = $el->Add($arLoadProductArray) ;
		$result[] = $ELEMENT_ID;
		return $result;
	}

	//переопределяемый метод. Задаем привязку свойств для каждого инфоблока.
	function SetProperty($data){
		$PR['ID_B24'] = $data['ID'];
		$PR['ENTITY_ID'] = $data['ENTITY_ID'];
		$PR['STATUS_ID'] = $data['STATUS_ID'];
		$PR['NAME_INIT'] = $data['NAME_INIT'];
		$PR['SYSTEM'] = $data['SYSTEM'];
		$PR['MODEL'] = $data['MODEL'];
		return $PR;
	}
}
?>