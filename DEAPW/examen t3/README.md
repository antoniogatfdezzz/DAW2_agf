# Antonio Gat Fernández

## Ejercicio 1
En este ejercicio he tenido que crear un documento .html en el que aparezca nuestro nombre a través de la IP pública de nuestra máquina EC2 de AWS. 


## Ejercicio 2
En este ejercicio tenemos que publicar en nuestro servidor DuckDNS nuestro apache para que sea visible "cosa.antoniogatfdezzz.duckdns.org" (que sería la que habría usado si me funcionase DuckDNS). Finalmente he tenido que usar cosa.antonio.luisferreira.top, debido a la falla de DuckDNS.
He tenido que hacer los cambios correspondientes en el archivo "apache/docker-compose.yml" para usar la URL:   cosa.antonio.luisferreira.top 
![Texto Alternativo](/img/duckdns1.png)

En el archivo "txts/apartado2.txt" puedes ver una prueba del "curl" que he realizado sobre la página usando:
curl https://cosa.antonio.luisferreira.top/ | tee apartado2.txt
![Texto Alternativo](/img/duckdns2.png)

También en el archivo "txts/apartado4.txt" puedes ver una prueba del "curl -I" que he realizado sobre la página usando:
curl https://cosa.antonio.luisferreira.top/ | tee apartado4.txt
![Texto Alternativo](/img/duckdns3.png)


## Ejercicio 3
En este ejercicio tenemos que ocultar las cabeceras cuando accedamos a cosa.antonio.luisferreira.top.

Para ello debemos introducir en el archivo de configuracion de apache lo siguiente:

```
ServerTokens Prod
ServerSignature Off
TraceEnable Off
```

![Texto Alternativo](/img/cabeceras2.png)


![Texto Alternativo](/img/cabeceras1.png)


## Ejercicio 4
En este ejercicio he tenido que crear una página de monitorización y direccionarla a "monitor.antoniogatfdezzz.duckdns.org" (que sería la que habría usado si me funcionase DuckDNS). Finalmente he tenido que usar monitor.antonio.luisferreira.top, debido a la falla de DuckDNS.

![Texto Alternativo](/img/kuma1.png)

Al visitar monitor.antoniogatfdezzz.duckdns.org llegamos a la siguiente página, en la que nos tenemos que loggear con las siguientes credenciales:
 * Usuario: antoniogatfdezzz
 * Contraseña: AGf123456

![Texto Alternativo](/img/kuma2.png)

Al introducir las credenciales, nos llevará a la página principal donde tendremos el contenedor de "apache" y la página: https://cosa.antonio.luisferreira.top/

![Texto Alternativo](/img/kuma3.png)


// Antonio Gat Fernández 