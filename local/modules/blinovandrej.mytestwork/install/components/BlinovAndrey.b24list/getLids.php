<?
$data = $_REQUEST;
if (isset($data["leadStatus"]))
{
	if(empty($_SERVER["DOCUMENT_ROOT"])){
		$_SERVER["DOCUMENT_ROOT"] = __DIR__ . "/../../../";
	}
	$result = "";
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	if(Cmodule::includeModule('blinovandrej.mytestwork')){
		$method = 'crm.deal.list';
		$params = array("STAGE_ID"=>$data["leadStatus"]);
		$result = blinovandrej\mytestwork\bwork::GetData($method,$params);

		$dataArr = [];
		//формируем и дополняем массив со сделками
		foreach($result['result'] as $key=>$leadItem){
			$dataArr[$key] = array(
				"NAME"=>$leadItem['TITLE'],
				"SUMM"=>$leadItem["OPPORTUNITY"],
				"CLIENT"=>$leadItem["ASSIGNED_BY_ID"],
				"DATECREATED"=>$leadItem["BEGINDATE"],
				"COMMENT"=>$leadItem["COMMENTS"],
				"ID"=>$leadItem["ID"],
			);
			$method = 'crm.productrow.list';
			$params = array("OWNER_ID"=>$leadItem['ID'],"OWNER_TYPE"=>"D");
			$resultProduct = blinovandrej\mytestwork\bwork::GetData($method,$params);
			$product=[];
			foreach($resultProduct['result'] as $productItem){
				$product[] = array(
					"NAME"=>$productItem['PRODUCT_NAME'],
					"PRICE"=>$productItem['PRICE'],
					"QUANTITY"=>intval($productItem['QUANTITY']),
					"ID"=>$productItem['PRODUCT_ID'] ,
					"MEASURE_NAME"=>$productItem["MEASURE_NAME"],
				);
			}
			$dataArr[$key]["PRODUCTS"] = $product;
		}

		echo(json_encode($dataArr));
	}
}

?>