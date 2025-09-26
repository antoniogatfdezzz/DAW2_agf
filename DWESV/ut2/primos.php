<html>
<head>
    <meta charset=utf-8 />
	<meta name=viewport content='width=device-width, initial-scale=1' />
	<meta name=author content='Antonio Gat  <me@antoniogatfdez.me' />
    <title>Números Primos</title>
</head>
<body>
    <?php
     // Sacar todos los primos entre 1 y 100
    echo "<h2>Números primos entre 1 y 100</h2>";
    for ($num = 2; $num <= 100; $num++) {
        $esPrimo = true;
        for ($i = 2; $i <= sqrt($num); $i++) {
            if ($num % $i == 0) {
                $esPrimo = false;
                break;
            }
        }
        if ($esPrimo) {
            echo "$num ";
        }
    }
    ?>
</body>
</html>