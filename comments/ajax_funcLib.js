sucessful = false;
errorDelay = 10000;

function insertNewData(params, php_script_path, targetHTMLid, method, callback) {
	request = new ajaxRequest(); /*Создаем новый обьект запроса (функция снизу)*/
	request.open(method, php_script_path, true); /*Настраиваем обьект на создаение post запроса по адресу файла php сценария. true - указывает на включение асинхронного режима*/
	request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	/*Отправляем http заголовки, для того чтобы сервер знал о поступлении POST запроса*/
	request.onreadystatechange = makeAjaxRequest(request, callback, targetHTMLid); //Передаем сюда колбек функцию
	request.send(params); /*Собственно отправка запроса*/

}

function makeAjaxRequest(request, callback, targetHTMLid) { //Цель этой функции - распаковать атрибуты внутрь объекта onreadystatechange
	//Эта функция возвращает свойству onreadystatechange анонимную функцию описанную ниже, уже с нашими параметрами
	return function() {
		if (request.readyState == 4) {
			if (request.status == 200) {
				if (request.responseText != null || request.responseText == true) {
					if (targetHTMLid != null) { //Если есть необходимость делать вставку в документ
						if (request.responseText !== "") {
							document.getElementById(targetHTMLid).innerHTML = request.responseText;
							if(callback != undefined)
								callback(true);
						} else {
							if(callback != undefined)
								callback(false);
							alert("Ошибка AJAX: Нет данных.");
						}
					} else {
						if(callback != undefined){
						callback(request.responseText);
					}
					}
				} else {
					alert("Ошибка AJAX: Данные не получены");
				}
			} else {
				alert("Ошибка AJAX: " + request.statusText);
			}
		}
	}
}


function ajaxRequest() {
	try {
		var request = new XMLHttpRequest()
	} catch (e1) {
		try {
			request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e2) {
			try {
				request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e3) {
				request = false;
			}
		}
	}
	return request;
}

function errorHandler(errorText) {
	alert("Ошибка AJAX: " + errorText);
}

function banPressed(comment_id, user_id, param, action_script_url) {
	var form = $("#form_" + comment_id);
	var radios = $("#form_" + comment_id + " input[name=" + param + "]");
	switch (param) {
		case 'ban':
			for (var i = 0; i != radios.lenght; ++i) {
				if (radios[i].checked == true) {
					insertNewData(param + '=' + radios[i].value + '&' + 'user_id=' + user_id + '&' + 'comment_id='+comment_id,
                        action_script_url,
                        'list',
                        'POST');
					break;
				}
			}
			break;
		case 'delete':
			insertNewData(param + '=' + comment_id, action_script_url, 'list', 'POST');
			break;
		case 'unban_user':
			insertNewData(param + '=' + user_id,action_script_url, 'list', 'POST');
			break;
	}
}

function getCookie(name) {
	var matches = document.cookie.match(new RegExp(
		"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
	));
	return matches ? decodeURIComponent(matches[1]) : undefined;
}

function getURIParameterByName(name) {
	name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
	var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
		results = regex.exec(location.search);
	return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}