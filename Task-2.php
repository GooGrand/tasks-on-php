<?php

$data = "It's it its we we'd we're";
$num = 5;

function preparedWord($word) {
    $clearApostroph = explode("'", $word);
    return $clearApostroph[0];
}

function countWord($data, $num) {
    $counter = 0;

    $lowered = strtolower($data);
    $words = explode(' ', $lowered);

    if(!isset($words[$num-1])){
        return 'This text is too short for this number';       // На случай если будет задан слишком короткий текст для числа
    }

    $query_word = preparedWord($words[$num - 1]);
    
    foreach($words as $word){

        $clean_word = preparedWord(trim($word, "!?&.,:- "));

        if($clean_word == $query_word){
            $counter++;
        }
    }
    return 'Word is '.$query_word.'. It\'s used '.$counter.' times';
}

echo countWord($data, $num);
