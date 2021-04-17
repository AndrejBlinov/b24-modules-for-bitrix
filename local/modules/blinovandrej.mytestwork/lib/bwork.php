<?
namespace blinovandrej\mytestwork ;
use Bitrix\Main\Config\Option ;

interface b24Get {
	public function GetData($method , $params ) ;
}
class bwork implements b24Get{


	private function CurlExec($queryData){
		$url = Option::get("blinovandrej.mytestwork", "webhkUrl");
		$queryUrl = $url.$queryData['method'] ;
		$curl = curl_init();
		curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $queryUrl,
            CURLOPT_POSTFIELDS => http_build_query($queryData)));
		$result = curl_exec($curl);
        $request = json_decode($result, true);
        return $request;
	}
	
	private function GetDataForB24($params){
		$result = self::CurlExec($params);
		return $result;
	}
	
	public function GetData($method, $params){
		$data['filter'] = $params;
		$data['method'] = $method;
		$result = self::GetDataForB24($data);
		return $result ;
	}
}

?>