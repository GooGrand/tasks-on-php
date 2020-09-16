<?php

// Можно еще через file_get_contents, но этот метод не вполне удобен
// Тестировал на локальном сервере, все вроде как работало. Прошу сообщить погрешности этого задания

$json = '{"urlFrom": "http://localtest:81/", "urlTo": "https://akiton.ru/post_dump.php", "word": "arasse"}';

function getPageByUrl ($url)
	{
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);

        //Выполняем запрос:		
        $result = curl_exec($curl);
        curl_close($curl);

		if ($result === false) {
            echo "Ошибка CURL: " . curl_error($curl);
			return false;
		} else {
			return $result;
		}
    }
    
function postParams($url, $param){

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query(array('wordcount'=> $param)),
        CURLOPT_SSL_VERIFYHOST => false,              // использовал по причине отсутстви ssl
        CURLOPT_SSL_VERIFYPEER => false,              //та же причина
    ));

    $response = curl_exec($curl);

    if ($response === false) {
        echo "Ошибка CURL подключение: " . curl_error($curl);
        return false;
    } else {
        return $response;
    }
    curl_close($curl);
    return $response;
}

function parcePTag($query) {
    $decoded = json_decode($query, true);       // Декодируем полученную строку json в массив
    $html = getPageByUrl($decoded['urlFrom']);  // Соединяемся с нужной страницей и получаем ее html с помощью функции, написанной выше
    $counter = 0;                               // Счетчик предложений с указанным словом
    
    if(!$html) {
        return 'Error happened with connection. Given url is incorrect or web-server is not working';
    }

    $dom = new DOMDocument;
    $dom->loadHTML($html);
    $paragraphs = $dom->getElementsByTagName('p');

    foreach($paragraphs as $tagText) {
        $lowered = strtolower($tagText->nodeValue);
        $sentences = explode('.', $lowered);

        foreach($sentences as $sentence) {
            $words = explode(' ', $sentence);
            foreach($words as $word){
                trim($word, "!?&.,:- ");
            }
            if (in_array($decoded['word'], $words)) {
                $counter++;
            }
        }
    }
    $result = postParams($decoded['urlTo'], $counter);
    echo $result;
    if(!$result) {
        return 'Check the urlTo params. Didn\'t connect to the server';
    }
    return "The word '".$decoded['word']."' is in ".$counter." sentence(s)";

}

echo parcePTag($json);