<template>
  <v-container class="fill-height" fluid>
    <v-row align="center" justify="center">
      <v-col cols="12" sm="8" md="4">
        <v-card class="elevation-12">
          <v-toolbar color="primary" dark flat>
            <v-toolbar-title>SIAP-Siswa Login</v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-form @submit.prevent="login">
              <v-text-field
                v-model="username"
                label="Username"
                prepend-icon="mdi-account"
                type="text"
                required
              ></v-text-field>
              <v-text-field
                v-model="password"
                label="Password"
                prepend-icon="mdi-lock"
                type="password"
                required
              ></v-text-field>
            </v-form>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="primary" @click="login">Login</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from '../api/axios'

export default {
  name: 'LoginView',
  data() {
    return {
      username: '',
      password: ''
    }
  },
  methods: {
    async login() {
      try {
        const response = await axios.post('/login.php', {
          username: this.username,
          password: this.password
        })

        if (response.data.success) {
          localStorage.setItem('user', JSON.stringify(response.data.user))
          this.redirectBasedOnRole(response.data.user.role)
        } else {
          alert(response.data.error || 'Login failed')
        }
      } catch (error) {
        console.error('Login error:', error)
        if (error.response && error.response.data && error.response.data.error) {
          alert(error.response.data.error)
        } else {
          alert('Login failed')
        }
      }
    },
    redirectBasedOnRole(role) {
      if (role === 'admin') {
        this.$router.push('/admin')
      } else if (role === 'guru') {
        this.$router.push('/guru')
      } else if (role === 'orangtua') {
        this.$router.push('/orangtua')
      }
    }
  }
}
</script>
