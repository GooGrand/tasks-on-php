<?php 
$arr = [1, 2, 7, 5, 6, 8, 4, 3, 9, 15, 22, 41, 21]; // Входной массив для теста

function isEven($num) {
    return $num % 2 == 0;    // Определяет четное или нечетное число
}

function reverseArray($array) {

    $odd = [];  // Нечетные
    $even = []; // Четные

    foreach ($array as $item) {         // Перебор массива с целью распределения четных и нечетных
        if(isEven($item))
        {
            $even[] = $item;
        } else {
            $odd[] = $item;
        }
    }
    asort($even);                       // Сортировка массивов в нужном порядке
    arsort($odd);
    $index = 0;
    foreach ($array as $item) {         // Каждый элемент оригинального массива заменяем на чет/нечет по условию
        if(isEven($item)) 
        {
            $array[$index] = array_shift($even);
        } else {
            $array[$index] = array_shift($odd);
        }
        $index++ ;
    }
    return $array;
}


echo implode(', ', $arr).'   ';
echo implode(', ', reverseArray($arr));