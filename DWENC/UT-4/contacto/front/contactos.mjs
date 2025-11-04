
fetch ('http://localhost:3000/contactos')
    .then(response => {
        return response.text();
    })
    .then(data => {
        const datos = JSON.parse(data);
        document.getElementById('contactos').innerHTML = datos[0].empresa;
    })
   
    //Obtiene una promesa de que obtendrá el texto
    //.then(data => console.log(data));  //Recibe el texto y lo procesa

    let url = 'https://example.com/contactos';
    let data = {
        nombre: 'Miguel',
        apellidos: 'González',
    };

    fetch(url, {
        method: 'POST', // or 'PUT'
        body: JSON.stringify(data), // data can be `string` or {object}!
        headers:{
        'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .catch(error => console.error('Error:', error))
    .then(response => console.log('Success:', response));