const express = require('express')
const app = express()
const port = 3000

app.get('/', (req, res) => {
  res.send('¡Hola Mundo, Antonio Gat Fernandez! - NODEJS')
})

app.listen(port, () => {
  console.log(`Escuchando puerto: ${port}`)
})
