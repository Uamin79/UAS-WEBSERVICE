import axios from 'axios'

const instance = axios.create({
  baseURL: 'http://localhost/siap-siswa/backend/api.php'
})

export default instance
