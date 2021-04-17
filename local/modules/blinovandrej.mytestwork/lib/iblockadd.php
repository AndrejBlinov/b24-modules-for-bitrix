<?
namespace blinovandrej\mytestwork ;
use Bitrix\Main ;

Bitrix\Main\cmodule::includeModule('iblock')

class IblockWork{
    private $iblock_id ;
    private $SectionID_forStatusList ;

    constructor ($iblock_id,$SectionID_forStatusList){
        $this->$iblock_id = $iblock_id;
        $this->$SectionID_forStatusList = $SectionID_forStatusList;
    }
    //добавляем элементы в инфоблок
    private function addIblock($list) {
        foreach($list as $data)
        {
            $PR = array();
			$PR = self::SetProperty($data);
            $el = self::GetIblockClass();
            $arLoadProductArray = Array(
                // "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
                "PROPERTY_VALUES"=> $PR,
				"NAME"           => !empty($data['NAME_INIT']) ? $data['NAME_INIT'] : $data['STATUS_ID'] ,
				"ACTIVE"         => "Y",            // активен
				"IBLOCK_ID"		=> COption::GetOptionString("blinovandrej.mytestwork", "iblockCreated"),
				"CODE"			=> $data['STATUS_ID'],
				"PREVIEW_TEXT" => $data['ID_B24'] ,
			);
            $ELEMENT_ID = $el->Add($arLoadProductArray) ;
            $result[] = $ELEMENT_ID;
        }
        return $result;
    }

    //Удаляем все элементы с инфоблока
    private function deleteIblock(){
        $arSelect = Array("ID","NAME",'IBLOCK_ID');//IBLOCK_ID и ID обязательно должны быть указаны, см. описание arSelectFields выше
        $arFilter = Array("IBLOCK_ID"=>$this->$iblock_id);
        $res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
        while($ob = $res->GetNextElement())
        { 
            $fields = $ob->GetFields();
            CIBlockElement::Delete($fields["ID"]);
        }
    }

    private function GetIblockClass(){
        $el = new CIBlockElement;
        return  $el;
    }

    public function UpdateIblock($data){
        //тк у нас мало статусов и обновление предпологается 1 раз в день, то удаляем и записываем заного
        $res = $this->deleteIblock(); 
        //Если есть и отличаются - обновляем
        $this->addIblock($data) ;
    }

    //переопределяемый метод. Задаем привязку свойств для каждого инфоблока.
    protected function SetProperty($data){
        $PR['ID_B24'] = $data['ID'];
        $PR['ENTITY_ID'] = $data['ENTITY_ID'];
        $PR['STATUS_ID'] = $data['STATUS_ID'];
        $PR['NAME_INIT'] = $data['NAME_INIT'];
        $PR['SYSTEM'] = $data['SYSTEM'];
        $PR['MODEL'] = $data['MODEL'];
        return $PR;
    }


?>