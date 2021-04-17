<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$this->setFrameMode(true);
?>

<?
//require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
//Вдруг не подключен Jquery и bootstrap
use Bitrix\Main\UI\Extension;
CJSCore::Init(array("jquery"));
Extension::load('ui.bootstrap4');
//Подключаем свои скрипты и стили
$APPLICATION->AddHeadScript(__DIR__ . "/script.js");
$APPLICATION->SetAdditionalCSS(__DIR__ . "/style.css");


$APPLICATION->SetTitle("Тестовая страница по получению информации по сделкам из Б24.");

?>
<div class="container">
  <div class="row">
    <div class="col-xl-2 col-lg-2 col-md-4 col-sm-4 buttonlist">
		<?foreach($arResult as $arItem){?>
      		<button onClick="GetData('<?=$arItem["CODE"]?>')" type="button" class="btn btn-secondary b24LeadButtons"><?=$arItem["NAME"]?></button>
		<?}?>
	  <!-- btn-primary -->
    </div>
    <div class="col-xl-10 col-lg-10 col-md-8 col-sm-8">
		<div id="b24LeadList" >

		</div>
    </div>
  </div>
</div>



