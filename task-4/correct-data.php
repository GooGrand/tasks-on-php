<?php

// require_once 'read-sheet.php';
// require_once 'write-sheet.php';
//пример
// Было 18615 Rogers Place SanAntonio Tx . 78258            Стало:  18615 Rogers Pl, San Antonio, TX 78258
//      477 south clinton st 2nd east orange, nj 07018            477 S Clinton St #2ndfl, East Orange, NJ 07018
//      3113 40th ave Meridian,MS 39307                             3113 40th Ave, Meridian, MS 39307
//      2479 Peachtree Rd NE Atlanta, Ga 30305                      2479 Peachtree Rd NE, Atlanta, GA 30305
// 
// 
// 
// 

$sheet = ['18615 Rogers Place SanAntonio Tx . 78258', '477 south clinton st 2nd east orange, nj 07018',
          '3113 40th ave Meridian,MS 39307', '2479 Peachtree Rd NE Atlanta, Ga 30305'];
$sheet_id = '1A9PCwXwF32Nag6mtgMwBr8G1ZXvz_vn23XeqDW_Cdz4';
// Получить

// $sheet = read_sheet($sheet_id);
function correct_data($sheet) {
    $finals = [];
// Разделить по группам (пробел) номер, (улица, дорога, эйв, плейс, вообще еще есть court (ct)), город, штат (2 буквы), zip
    foreach($sheet as $line){
        $words = preg_split("/[\s,\.]+/", $line);
        $mistypes = array('.', ',', '&', ' ' );     // Это нужно только в том случае, если у нас одиночная точка. Для третьего случая это все ломает
        foreach($words as $word){
            trim($word, ", .:!?/");
            $word = str_replace($mistypes, '', $word);
            strtolower($word);
            ucfirst($word);
            if(empty($word)){
                unset($words[$word]);
            }
        }

        // echo implode(' ', $words);

        if(!is_numeric($words[0])){
            return 'Incorrect house number';
        }
        $house_num = array_shift($words);

        // echo implode(' ', $words);

        if(!is_numeric(end($words))){
            return 'Incorrect zip';
        }
        $zip = array_pop($words);

        // echo implode(' ', $words);
        echo strlen(end($words));
        

        if(strlen(end($words)) != 2){ 
            if(strlen(end($words)) == 0) {
                array_pop($word);
            } else {
                return 'Incorrect state. Use postal abbreviation';
            }                                   // Проверка на двухбуквенность штата. В идеале иметь библиотеку с проверкой и автозаполнением,
                                                 // но мне кажется это излишним в данном задании. Хотя я бы сделал
        }
        $state = strtoupper(array_pop($words));

        array_push($finals, $house_num. ' ' . implode(' ', $words) . ', ' . $state . ' ' . $zip);

// Далее есть набор слов, часть которых относится к улице, другая - к городу
// Починить, в массив
    }
    return $finals;


// Массивом запостить в гугл
}
echo implode(' ', correct_data($sheet));