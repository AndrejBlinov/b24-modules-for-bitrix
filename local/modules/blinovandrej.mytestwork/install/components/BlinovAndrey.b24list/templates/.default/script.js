$( document ).ready(function() {
	console.log(1);
});


//Отработка нажатии кнопок.
function GetData(e){
	let result = get(e);
}
//1) прописать post запрос с передачей статуса лида
function get(date){
	const url = "/local/components/BlinovAndrey.b24list/getLids.php";
	$.post( url, {leadStatus: date })
	.done(function( data ) {
		let arr = jQuery.parseJSON(data);
		WriteBlock(arr);
	});
}

//2) отрисовка запроса
function WriteBlock(arr){
	let str ='<table class="table table-hover"><thead><tr><th>#</th><th>Наименование</th><th>CLIENT ID</th><th>Дата создания</th><th>Комментарий</th><th>Список товаров</th></tr></thead>';
	str += '<tbody>';
	$.each(arr, function(index, value){
		str += '<tr><th scope="row">' + value['ID'] + '</th>';
		str += '<td rowspan="2">'+ value['NAME']+ '</td>';
		str += '<td rowspan="2">'+ value['CLIENT']+ '</td>';
		str += '<td rowspan="2">'+ value['DATECREATED']+ '</td>';
		str += '<td rowspan="2">'+ value['COMMENT']+ '</td>';
		if (value['PRODUCTS'].length !== 0)
		{
			str += '<td rowspan="2">';
			$.each(value['PRODUCTS'], function(productIndex, productValue){
				str += '<div class="tovar-list">Товар:'+ productValue['NAME'] + ' Цена:' + productValue['PRICE'] + '|'+ productValue['QUANTITY'] + productValue['MEASURE_NAME'] +'</div>';
			});
			str += '</td>';
		}
		str += '</tr>';
		str += '<tr><td >Итого: '+ value['SUMM']+ '</td></tr>';
	});
	str +='</tbody></table>';
	$("#b24LeadList").html(str);
}
