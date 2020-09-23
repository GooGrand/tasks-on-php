<?php


$sheet = ['18615 Rogers Place SanAntonio Tx . 78258', '477 south clinton st 2nd east orange, nj 07018',
          '3113 40th ave Meridian,MS 39307', '2479 Peachtree Rd NE Atlanta, Ga 30305'];

function prepareUrl($address)
{
    $parts = preg_split("/[\s,]+/", $address);
    $urlAddress = implode('%20', $parts);
    $url = 'https://www.google.com/search?tbm=map&authuser=0&hl=en&gl=ru&q=' . $urlAddress . '&oq=' . $urlAddress . '&gs_l=maps.12..38i10i428k1j38i443i428k1l4.23689.23689.1.38082.5.5.0.0.0.0.207.207.2-1.5.0....0...1ac.1.64.maps..0.5.1095.3..38i39k1.109.';
    return $url;
}

function getPageByUrl ($url)
{
    $curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

    //Выполняем запрос:		
    $result = curl_exec($curl);

	if ($result === false) {
        echo "Ошибка CURL: " . curl_error($curl);
        curl_close($curl);
		return false;
	} else {
        curl_close($curl);
		return $result;
	}
}
foreach($sheet as $postal)
{
    $url = prepareUrl($postal);
    $json_string = getPageByUrl($url);
    // $json_array = explode(']', $json_string);
    // $address = str_replace($filter_array, '', $json_array[24]);  // Пробовал перебирать массив, но результат зависел от запроса
    $address_with_tail = stristr($json_string, ',[2,[["');
    $address_with_needle = stristr($address_with_tail, ', USA"', true);
    $address = str_replace(',[2,[["', '', $address_with_needle);
    echo $address;
}

