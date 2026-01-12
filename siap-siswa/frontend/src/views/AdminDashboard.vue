<template>
  <v-app>
    <v-navigation-drawer v-model="drawer" app>
      <v-list>
        <v-list-item>
          <v-list-item-content>
            <v-list-item-title class="text-h6">Admin Dashboard</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-divider></v-divider>
        <v-list-item @click="activeTab = 'students'">
          <v-list-item-icon><v-icon>mdi-account-school</v-icon></v-list-item-icon>
          <v-list-item-content><v-list-item-title>Students</v-list-item-title></v-list-item-content>
        </v-list-item>
        <v-list-item @click="activeTab = 'teachers'">
          <v-list-item-icon><v-icon>mdi-teach</v-icon></v-list-item-icon>
          <v-list-item-content><v-list-item-title>Teachers</v-list-item-title></v-list-item-content>
        </v-list-item>
        <v-list-item @click="activeTab = 'parents'">
          <v-list-item-icon><v-icon>mdi-account-group</v-icon></v-list-item-icon>
          <v-list-item-content><v-list-item-title>Parents</v-list-item-title></v-list-item-content>
        </v-list-item>
        <v-list-item @click="activeTab = 'classes'">
          <v-list-item-icon><v-icon>mdi-school</v-icon></v-list-item-icon>
          <v-list-item-content><v-list-item-title>Classes</v-list-item-title></v-list-item-content>
        </v-list-item>
        <v-list-item @click="activeTab = 'subjects'">
          <v-list-item-icon><v-icon>mdi-book-open-page-variant</v-icon></v-list-item-icon>
          <v-list-item-content><v-list-item-title>Subjects</v-list-item-title></v-list-item-content>
        </v-list-item>
        <v-divider></v-divider>
        <v-list-item @click="logout">
          <v-list-item-icon><v-icon>mdi-logout</v-icon></v-list-item-icon>
          <v-list-item-content><v-list-item-title>Logout</v-list-item-title></v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-app-bar app>
      <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
      <v-toolbar-title>SIAP-Siswa Admin</v-toolbar-title>
    </v-app-bar>

    <v-main>
      <v-container>
        <v-tabs v-model="activeTab">
          <v-tab value="students">Students</v-tab>
          <v-tab value="teachers">Teachers</v-tab>
          <v-tab value="parents">Parents</v-tab>
          <v-tab value="classes">Classes</v-tab>
          <v-tab value="subjects">Subjects</v-tab>
        </v-tabs>

        <v-tabs-items v-model="activeTab">
          <v-tab-item value="students">
            <DataTable
              :headers="studentHeaders"
              :items="students"
              :loading="loading"
              @add="openStudentDialog"
              @edit="openStudentDialog"
              @delete="deleteStudent"
            />
          </v-tab-item>
          <v-tab-item value="teachers">
            <DataTable
              :headers="teacherHeaders"
              :items="teachers"
              :loading="loading"
              @add="openTeacherDialog"
              @edit="openTeacherDialog"
              @delete="deleteTeacher"
            />
          </v-tab-item>
          <v-tab-item value="parents">
            <DataTable
              :headers="parentHeaders"
              :items="parents"
              :loading="loading"
              @add="openParentDialog"
              @edit="openParentDialog"
              @delete="deleteParent"
            />
          </v-tab-item>
          <v-tab-item value="classes">
            <DataTable
              :headers="classHeaders"
              :items="classes"
              :loading="loading"
              @add="openClassDialog"
              @edit="openClassDialog"
              @delete="deleteClass"
            />
          </v-tab-item>
          <v-tab-item value="subjects">
            <DataTable
              :headers="subjectHeaders"
              :items="subjects"
              :loading="loading"
              @add="openSubjectDialog"
              @edit="openSubjectDialog"
              @delete="deleteSubject"
            />
          </v-tab-item>
        </v-tabs-items>
      </v-container>
    </v-main>

    <!-- Student Dialog -->
    <v-dialog v-model="studentDialog" max-width="600px">
      <v-card>
        <v-card-title>{{ studentForm.id ? 'Edit Student' : 'Add Student' }}</v-card-title>
        <v-card-text>
          <v-form @submit.prevent="saveStudent">
            <v-text-field v-model="studentForm.nis" label="NIS" required></v-text-field>
            <v-text-field v-model="studentForm.full_name" label="Full Name" required></v-text-field>
            <v-select v-model="studentForm.gender" :items="['L', 'P']" label="Gender" required></v-select>
            <v-text-field v-model="studentForm.birth_date" label="Birth Date" type="date"></v-text-field>
            <v-text-field v-model="studentForm.address" label="Address"></v-text-field>
            <v-select v-model="studentForm.class_id" :items="classOptions" item-text="class_name" item-value="id" label="Class" required></v-select>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="studentDialog = false">Cancel</v-btn>
          <v-btn color="primary" @click="saveStudent">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Similar dialogs for teachers, parents, classes, subjects -->
  </v-app>
</template>

<script>
import axios from '../api/axios'
import DataTable from '../components/DataTable.vue'

export default {
  name: 'AdminDashboard',
  components: { DataTable },
  data() {
    return {
      drawer: true,
      activeTab: 'students',
      loading: false,
      students: [],
      teachers: [],
      parents: [],
      classes: [],
      subjects: [],
      studentDialog: false,
      teacherDialog: false,
      parentDialog: false,
      classDialog: false,
      subjectDialog: false,
      studentForm: {},
      teacherForm: {},
      parentForm: {},
      classForm: {},
      subjectForm: {},
      studentHeaders: [
        { text: 'NIS', value: 'nis' },
        { text: 'Name', value: 'full_name' },
        { text: 'Gender', value: 'gender' },
        { text: 'Class', value: 'class_name' },
        { text: 'Actions', value: 'actions', sortable: false }
      ],
      teacherHeaders: [
        { text: 'NIP', value: 'nip' },
        { text: 'Name', value: 'full_name' },
        { text: 'Email', value: 'email' },
        { text: 'Actions', value: 'actions', sortable: false }
      ],
      parentHeaders: [
        { text: 'Name', value: 'full_name' },
        { text: 'Email', value: 'email' },
        { text: 'Phone', value: 'phone_number' },
        { text: 'Actions', value: 'actions', sortable: false }
      ],
      classHeaders: [
        { text: 'Class Name', value: 'class_name' },
        { text: 'Homeroom Teacher', value: 'teacher_name' },
        { text: 'Actions', value: 'actions', sortable: false }
      ],
      subjectHeaders: [
        { text: 'Subject Name', value: 'subject_name' },
        { text: 'Description', value: 'description' },
        { text: 'Actions', value: 'actions', sortable: false }
      ]
    }
  },
  computed: {
    classOptions() {
      return this.classes.map(c => ({ id: c.id, class_name: c.class_name }))
    }
  },
  mounted() {
    this.loadData()
  },
  methods: {
    async loadData() {
      this.loading = true
      try {
        const [studentsRes, teachersRes, parentsRes, classesRes, subjectsRes] = await Promise.all([
          axios.get('/students'),
          axios.get('/teachers'),
          axios.get('/parents'),
          axios.get('/classes'),
          axios.get('/subjects')
        ])
        this.students = studentsRes.data.students.records
        this.teachers = teachersRes.data.teachers.records
        this.parents = parentsRes.data.parents.records
        this.classes = classesRes.data.classes.records
        this.subjects = subjectsRes.data.subjects.records
      } catch (error) {
        console.error('Error loading data:', error)
      } finally {
        this.loading = false
      }
    },
    openStudentDialog(student = {}) {
      this.studentForm = { ...student }
      this.studentDialog = true
    },
    async saveStudent() {
      try {
        if (this.studentForm.id) {
          await axios.put(`/students/${this.studentForm.id}`, this.studentForm)
        } else {
          await axios.post('/students', this.studentForm)
        }
        this.studentDialog = false
        this.loadData()
      } catch (error) {
        console.error('Error saving student:', error)
      }
    },
    async deleteStudent(student) {
      if (confirm('Are you sure you want to delete this student?')) {
        try {
          await axios.delete(`/students/${student.id}`)
          this.loadData()
        } catch (error) {
          console.error('Error deleting student:', error)
        }
      }
    },
    // Similar methods for teachers, parents, classes, subjects
    openTeacherDialog(teacher = {}) {
      this.teacherForm = { ...teacher }
      this.teacherDialog = true
    },
    async saveTeacher() {
      try {
        if (this.teacherForm.id) {
          await axios.put(`/teachers/${this.teacherForm.id}`, this.teacherForm)
        } else {
          await axios.post('/teachers', this.teacherForm)
        }
        this.teacherDialog = false
        this.loadData()
      } catch (error) {
        console.error('Error saving teacher:', error)
      }
    },
    async deleteTeacher(teacher) {
      if (confirm('Are you sure you want to delete this teacher?')) {
        try {
          await axios.delete(`/teachers/${teacher.id}`)
          this.loadData()
        } catch (error) {
          console.error('Error deleting teacher:', error)
        }
      }
    },
    logout() {
      localStorage.removeItem('user')
      this.$router.push('/')
    }
  }
}
</script>
