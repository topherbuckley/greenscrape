<?php
//$arr = array('a'=>'A','b'=>'B','c'=>'C');
$arr = array('A','B','C');
$key = 1;
echo "Original Array = ";
print_r($arr);

array_splice($arr, $key+1, 0, 'others');
echo "<br>others added Array = ";
print_r($arr);

array_splice($arr, $key+1, 0, 'city');
echo "<br>city added Array = ";
print_r($arr);

array_splice($arr, $key+1, 0, 'prefecture');
echo "<br>prefecture added Array = ";
print_r($arr);

array_splice($arr, $key+1, 0, 'zip');
echo "<br>zip added Array = ";
print_r($arr);
?>