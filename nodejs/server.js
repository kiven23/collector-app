// backend/index.js
const express = require('express')
const cors = require('cors')
const { PhAddress } = require('ph-address')

const app = express()
app.use(cors())

app.get('/find-address', async (req, res) => {
  const query = req.query.q || ''
  const phAddress = new PhAddress()
  const addressFinder = await phAddress.useSqlite()
  const results = await addressFinder.find(query)
  res.json(results)
})

app.listen(3000, () => console.log('Server running on port 3000'))
