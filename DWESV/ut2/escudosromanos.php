<?php
$ejercito = 0;
$total = 0;
$temp = 0;

while (true) {
    $ejercito = intval(fgets(STDIN));
    if ($ejercito == 0) break;
    
    $total = 0;
    
    while (true) {
        if ($ejercito == 0) break;
        
        if ($ejercito < 4) {
            $total += $ejercito * 5;
            break;
        }
        
        $temp = intval(sqrt($ejercito));
        $ejercito -= ($temp * $temp);
        $total += ($temp - 2) * ($temp - 2); // interior
        $total += ((($temp - 2) * 4) * 2) + 12; // exterior + esquinas
    }
    
    echo $total . "\n";
}
?>