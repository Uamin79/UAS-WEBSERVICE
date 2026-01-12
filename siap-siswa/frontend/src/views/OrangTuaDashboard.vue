<template>
  <v-app>
    <v-navigation-drawer v-model="drawer" app>
      <v-list>
        <v-list-item>
          <v-list-item-content>
            <v-list-item-title class="text-h6">Orang Tua Dashboard</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-divider></v-divider>
        <v-list-item @click="activeTab = 'children'">
          <v-list-item-icon><v-icon>mdi-account-child</v-icon></v-list-item-icon>
          <v-list-item-content><v-list-item-title>My Children</v-list-item-title></v-list-item-content>
        </v-list-item>
        <v-list-item @click="activeTab = 'attendance'">
          <v-list-item-icon><v-icon>mdi-clipboard-check</v-icon></v-list-item-icon>
          <v-list-item-content><v-list-item-title>Attendance</v-list-item-title></v-list-item-content>
        </v-list-item>
        <v-list-item @click="activeTab = 'grades'">
          <v-list-item-icon><v-icon>mdi-chart-line</v-icon></v-list-item-icon>
          <v-list-item-content><v-list-item-title>Grades</v-list-item-title></v-list-item-content>
        </v-list-item>
        <v-list-item @click="activeTab = 'reports'">
          <v-list-item-icon><v-icon>mdi-file-document</v-icon></v-list-item-icon>
          <v-list-item-content><v-list-item-title>Report Cards</v-list-item-title></v-list-item-content>
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
      <v-toolbar-title>SIAP-Siswa Orang Tua</v-toolbar-title>
    </v-app-bar>

    <v-main>
      <v-container>
        <v-tabs v-model="activeTab">
          <v-tab value="children">Children</v-tab>
          <v-tab value="attendance">Attendance</v-tab>
          <v-tab value="grades">Grades</v-tab>
          <v-tab value="reports">Report Cards</v-tab>
        </v-tabs>

        <v-tabs-items v-model="activeTab">
          <v-tab-item value="children">
            <v-card class="mt-4">
              <v-card-title>My Children</v-card-title>
              <v-card-text>
                <v-data-table
                  :headers="childrenHeaders"
                  :items="children"
                  :loading="loading"
                  class="elevation-1"
                ></v-data-table>
              </v-card-text>
            </v-card>
          </v-tab-item>

          <v-tab-item value="attendance">
            <v-card class="mt-4">
              <v-card-title>Attendance Records</v-card-title>
              <v-card-text>
                <v-select
                  v-model="selectedChild"
                  :items="children"
                  item-text="full_name"
                  item-value="id"
                  label="Select Child"
                  @change="loadAttendance"
                ></v-select>
                <v-data-table
                  v-if="attendanceRecords.length"
                  :headers="attendanceHeaders"
                  :items="attendanceRecords"
                  class="elevation-1"
                ></v-data-table>
              </v-card-text>
            </v-card>
          </v-tab-item>

          <v-tab-item value="grades">
            <v-card class="mt-4">
              <v-card-title>Grade Progress</v-card-title>
              <v-card-text>
                <v-select
                  v-model="selectedChild"
                  :items="children"
                  item-text="full_name"
                  item-value="id"
                  label="Select Child"
                  @change="loadGrades"
                ></v-select>
                <v-data-table
                  v-if="gradeRecords.length"
                  :headers="gradeHeaders"
                  :items="gradeRecords"
                  class="elevation-1"
                ></v-data-table>
              </v-card-text>
            </v-card>
          </v-tab-item>

          <v-tab-item value="reports">
            <v-card class="mt-4">
              <v-card-title>Report Cards</v-card-title>
              <v-card-text>
                <v-select
                  v-model="selectedChild"
                  :items="children"
                  item-text="full_name"
                  item-value="id"
                  label="Select Child"
                  @change="loadReportCards"
                ></v-select>
                <v-card v-if="selectedReport" class="mt-4">
                  <v-card-title>{{ selectedReport.student_name }} - Semester {{ selectedReport.semester }}</v-card-title>
                  <v-card-text>
                    <p><strong>Academic Year:</strong> {{ selectedReport.academic_year }}</p>
                    <p><strong>Homeroom Teacher Notes:</strong></p>
                    <p>{{ selectedReport.homeroom_teacher_notes }}</p>
                    <v-textarea
                      v-model="parentReply"
                      label="Your Reply"
                      placeholder="Add your comments or questions here..."
                    ></v-textarea>
                    <v-btn color="primary" @click="saveParentReply">Save Reply</v-btn>
                  </v-card-text>
                </v-card>
              </v-card-text>
            </v-card>
          </v-tab-item>
        </v-tabs-items>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import axios from '../api/axios'

export default {
  name: 'OrangTuaDashboard',
  data() {
    return {
      drawer: true,
      activeTab: 'children',
      loading: false,
      children: [],
      selectedChild: null,
      attendanceRecords: [],
      gradeRecords: [],
      reportCards: [],
      selectedReport: null,
      parentReply: '',
      childrenHeaders: [
        { text: 'NIS', value: 'nis' },
        { text: 'Name', value: 'full_name' },
        { text: 'Gender', value: 'gender' },
        { text: 'Class', value: 'class_name' }
      ],
      attendanceHeaders: [
        { text: 'Date', value: 'date' },
        { text: 'Subject', value: 'subject_name' },
        { text: 'Status', value: 'status' },
        { text: 'Notes', value: 'notes' }
      ],
      gradeHeaders: [
        { text: 'Subject', value: 'subject_name' },
        { text: 'Assessment Type', value: 'assessment_type' },
        { text: 'Score', value: 'score' },
        { text: 'Date', value: 'grade_date' }
      ]
    }
  },
  computed: {
    currentUser() {
      return JSON.parse(localStorage.getItem('user'))
    }
  },
  mounted() {
    this.loadChildren()
  },
  methods: {
    async loadChildren() {
      this.loading = true
      try {
        // Get parent relations
        const relationsRes = await axios.get(`/student_parent_relations?filter=parent_id,eq,${this.currentUser.id}`)
        const studentIds = relationsRes.data.student_parent_relations.records.map(r => r.student_id)

        if (studentIds.length > 0) {
          // Get student details
          const studentsRes = await axios.get(`/students?filter=id,in,${studentIds.join(',')}`)
          this.children = studentsRes.data.students.records

          // Get class names
          const classIds = [...new Set(this.children.map(c => c.class_id))]
          const classesRes = await axios.get(`/classes?filter=id,in,${classIds.join(',')}`)
          const classes = classesRes.data.classes.records

          this.children = this.children.map(child => ({
            ...child,
            class_name: classes.find(c => c.id === child.class_id)?.class_name
          }))
        }
      } catch (error) {
        console.error('Error loading children:', error)
      } finally {
        this.loading = false
      }
    },
    async loadAttendance() {
      if (!this.selectedChild) return
      try {
        const attendanceRes = await axios.get(`/attendances?filter=student_id,eq,${this.selectedChild}`)
        this.attendanceRecords = attendanceRes.data.attendances.records

        // Get subject names
        const subjectIds = [...new Set(this.attendanceRecords.map(a => a.subject_id))]
        const subjectsRes = await axios.get(`/subjects?filter=id,in,${subjectIds.join(',')}`)
        const subjects = subjectsRes.data.subjects.records

        this.attendanceRecords = this.attendanceRecords.map(record => ({
          ...record,
          subject_name: subjects.find(s => s.id === record.subject_id)?.subject_name
        }))
      } catch (error) {
        console.error('Error loading attendance:', error)
      }
    },
    async loadGrades() {
      if (!this.selectedChild) return
      try {
        const gradesRes = await axios.get(`/grades?filter=student_id,eq,${this.selectedChild}`)
        this.gradeRecords = gradesRes.data.grades.records

        // Get subject names
        const subjectIds = [...new Set(this.gradeRecords.map(g => g.subject_id))]
        const subjectsRes = await axios.get(`/subjects?filter=id,in,${subjectIds.join(',')}`)
        const subjects = subjectsRes.data.subjects.records

        this.gradeRecords = this.gradeRecords.map(record => ({
          ...record,
          subject_name: subjects.find(s => s.id === record.subject_id)?.subject_name
        }))
      } catch (error) {
        console.error('Error loading grades:', error)
      }
    },
    async loadReportCards() {
      if (!this.selectedChild) return
      try {
        const reportsRes = await axios.get(`/report_cards?filter=student_id,eq,${this.selectedChild}`)
        this.reportCards = reportsRes.data.report_cards.records
        if (this.reportCards.length > 0) {
          this.selectedReport = this.reportCards[0]
          this.parentReply = this.selectedReport.parent_reply_notes || ''
        }
      } catch (error) {
        console.error('Error loading report cards:', error)
      }
    },
    async saveParentReply() {
      try {
        await axios.put(`/report_cards/${this.selectedReport.id}`, {
          parent_reply_notes: this.parentReply
        })
        alert('Reply saved successfully!')
        this.loadReportCards()
      } catch (error) {
        console.error('Error saving reply:', error)
        alert('Error saving reply')
      }
    },
    logout() {
      localStorage.removeItem('user')
      this.$router.push('/')
    }
  }
}
</script>
