# Descripción
Backend de aplicación de gestión de contactos. 

# API Rest
Las APIs REST son APIs que se apoyan en el protocolo HTTP para dar su servicio. Se soportan las operaciones HTTP para operaciones convencionales. En estas APIs, una **URL representa un recurso**, **el método HTTP representa la operación** a realizar y el **código de estado representa el resultado**.

### Recursos
En este backend se han incluido los siguientes recursos: 

- http://localhost:3000/contactos: Contactos.
- http://localhost:3000/telefonos: Teléfonos asociados a los contactos.
- http://localhost:3000/correos: Direcciones de correo asociadas a los contactos.

Directrices para escoger el nombre de los recursos: 
- Plural mejor que singular, para lograr uniformidad:
- Url's lo más cortas posibles
- Evita guiones y guiones bajos
- Deben ser semánticas para el cliente
- Utiliza nombres y no verbos

### Métodos/Operaciones
* **GET**: Permite cargar un recurso. Puede recibir los siguientes códigos como resultado resultado:
  - 200 ok
  - 404 No encontrado
  
* **POST**: Permite crear un recurso. La petición no debe tener identificador ya que el recurso no existía en la base de datos. En la cabecera HTTP **Location** de la respuesta contendrá la URL para acceder al rescurso recien creado. Nos puede devolver los siguientes códigos de resultado:
  - 201 Recurso creado correctamente
  - 400 Petición incorrecta
  - 403 Acceso prohibido
  - 500 Error en el servidor

+ **PUT**: Actualiza todos los datos excepto el identificador. Puede devolver los siguientes datos de resultado. 
  - 200 Recurso actualizado correctamente
  - 201 Recurso creado correctamente
  - 400 Petición incorrecta
  - 403 Acceso prohibido
  - 500 Error en el servidor

+ **PATCH**: Nuevo método. Permite actualizar solo una parte de los datos. No está soportado por todos los frameworks.

+ **DELETE**: Elimina el recurso. Después de eliminarlo, los intentos de acceso deberían fallar. Puede devolver los siguientes resultados:
    - 200 OK
    - 404 No encontrado
    - 500 Error en el servidor

### CORS
Por motivos de seguridad no es posible utilizar un backend alojado en otro dominio sin autorización previa de modo que será necesario configurar CORS en el backend que estemos utilizando.

# Instalación del servidor
Será necesario instalar el paquete json-server. Podemos hacerlo utilizando alguno de los siguientes comandos:

Si utilizamos el proyecto suministrado podemos utilizar el siguiente comando:
```cmd
C:> npm install 
```

En un nuevo proyecto podemos utilizar la siguiente para instalarlo en el directorio del proyecto:
```cmd
C:> npm install --prefix=. json-server
```


# Ejecución del servidor
Para ejecutar el servidor se puede lanzar el siguiente comando:

```shell
C:\....\> npx json-server --watch db.json
Debugger listening on ws://127.0.0.1:64447/387eb498-c271-4ca5-84e9-42131547f1fe
For help, see: https://nodejs.org/en/docs/inspector
Debugger attached.

  \{^_^}/ hi!

  Loading db.json
  Done

  Resources
  http://localhost:3000/contactos
  http://localhost:3000/telefonos
  http://localhost:3000/correos

  Home
  http://localhost:3000

  Type s + enter at any time to create a snapshot of the database
  Watching...
```
Nos ha dado tres url que podemos utilizar para acceder a cada uno de los recursos.

# Consultas
En este apartado incluyo algunas consultas que pueden hacerse.

### Contactos
- http://localhost:3000/contactos : Devuelve todos los contactos.
- http://localhost:3000/contactos?nombre=Paco&apellidos=García : Devuelve los contactos con nombre Paco y apellidos García
- http://localhost:3000/contactos/1 : Devuelve el contacto con ID = 1

### Teléfonos
- http://localhost:3000/telefonos?contacto.nombre=Paco : Los teléfonos asociados a nombre Paco
- http://localhost:3000/telefonos?contactoId=1 : Retorna los teléfonos del contacto 1.

### Elementos hijos
Esta url devuelve los contactos con los correos y teléfonos asociados:

http://localhost:3000/contactos?_embed=telefonos&_embed=correos

### Incluir padre
Permite obtener por ejemplo los teléfonos pero con los datos del padre relacionado.

http://localhost:3000/telefonos/GET?_expand=contacto

### Búsquedas
Con **gte** (mayor o igual) y **lte** (menor o igual) podemos obtener los contactos en una letra o hacer búsquedas

http://localhost:3000/contactos?nombre_gte=P&nombre_lte=Q

**ne** permite excluir un valor del resultado

http://localhost:3000/contactos?id_ne=1

**like** permite buscar. Soporta expresiones regulares.

http://localhost:3000/contactos?nombre_like=Pac

También disponemos de búsqueda de texto completa que busca el texto indicado en todos los campos. En este caso es el parámetro **q**

http://localhost:3000/contactos?q=Pac

### Ordenación
Podemos ordenar por diferentes campos 

http://localhost:3000/contactos?_sort=nombre,apellidos&_order=desc,asc

### Paginación
Si queremos implementar paginación, podemos utilizar urls como las siguientes:

http://localhost:3000/contactos?_page=7
http://localhost:3000/contactos?_page=7&_limit=20

# Bibliografía 
- API REST: https://juanda.gitbooks.io/webapps/content/api/arquitectura-api-rest.html
- json-server: https://github.com/typicode/json-server

