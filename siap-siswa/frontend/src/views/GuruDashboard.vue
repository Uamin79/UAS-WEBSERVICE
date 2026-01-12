<template>
  <v-app>
    <v-navigation-drawer v-model="drawer" app>
      <v-list>
        <v-list-item>
          <v-list-item-content>
            <v-list-item-title class="text-h6">Guru Dashboard</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-divider></v-divider>
        <v-list-item @click="activeTab = 'attendance'">
          <v-list-item-icon><v-icon>mdi-clipboard-check</v-icon></v-list-item-icon>
          <v-list-item-content><v-list-item-title>Attendance</v-list-item-title></v-list-item-content>
        </v-list-item>
        <v-list-item @click="activeTab = 'grades'">
          <v-list-item-icon><v-icon>mdi-book-open-variant</v-icon></v-list-item-icon>
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
      <v-toolbar-title>SIAP-Siswa Guru</v-toolbar-title>
    </v-app-bar>

    <v-main>
      <v-container>
        <v-tabs v-model="activeTab">
          <v-tab value="attendance">Attendance</v-tab>
          <v-tab value="grades">Grades</v-tab>
          <v-tab value="reports">Report Cards</v-tab>
        </v-tabs>

        <v-tabs-items v-model="activeTab">
          <v-tab-item value="attendance">
            <v-card class="mt-4">
              <v-card-title>Mark Attendance</v-card-title>
              <v-card-text>
                <v-select
                  v-model="selectedClass"
                  :items="teacherClasses"
                  item-text="class_name"
                  item-value="id"
                  label="Select Class"
                  @change="loadStudentsForAttendance"
                ></v-select>
                <v-select
                  v-model="selectedSubject"
                  :items="teacherSubjects"
                  item-text="subject_name"
                  item-value="id"
                  label="Select Subject"
                ></v-select>
                <v-text-field
                  v-model="attendanceDate"
                  label="Date"
                  type="date"
                  required
                ></v-text-field>
                <v-data-table
                  v-if="attendanceStudents.length"
                  :headers="attendanceHeaders"
                  :items="attendanceStudents"
                  class="elevation-1"
                >
                  <template v-slot:item.status="{ item }">
                    <v-select
                      v-model="item.status"
                      :items="['hadir', 'sakit', 'izin', 'alpa']"
                      dense
                    ></v-select>
                  </template>
                  <template v-slot:item.notes="{ item }">
                    <v-text-field v-model="item.notes" dense></v-text-field>
                  </template>
                </v-data-table>
                <v-btn v-if="attendanceStudents.length" color="primary" @click="saveAttendance" class="mt-4">Save Attendance</v-btn>
              </v-card-text>
            </v-card>
          </v-tab-item>

          <v-tab-item value="grades">
            <v-card class="mt-4">
              <v-card-title>Input Grades</v-card-title>
              <v-card-text>
                <v-select
                  v-model="selectedClass"
                  :items="teacherClasses"
                  item-text="class_name"
                  item-value="id"
                  label="Select Class"
                  @change="loadStudentsForGrades"
                ></v-select>
                <v-select
                  v-model="selectedSubject"
                  :items="teacherSubjects"
                  item-text="subject_name"
                  item-value="id"
                  label="Select Subject"
                ></v-select>
                <v-select
                  v-model="assessmentType"
                  :items="['tugas', 'uts', 'uas']"
                  label="Assessment Type"
                ></v-select>
                <v-text-field
                  v-model="gradeDate"
                  label="Grade Date"
                  type="date"
                  required
                ></v-text-field>
                <v-data-table
                  v-if="gradeStudents.length"
                  :headers="gradeHeaders"
                  :items="gradeStudents"
                  class="elevation-1"
                >
                  <template v-slot:item.score="{ item }">
                    <v-text-field
                      v-model="item.score"
                      type="number"
                      min="0"
                      max="100"
                      dense
                    ></v-text-field>
                  </template>
                </v-data-table>
                <v-btn v-if="gradeStudents.length" color="primary" @click="saveGrades" class="mt-4">Save Grades</v-btn>
              </v-card-text>
            </v-card>
          </v-tab-item>

          <v-tab-item value="reports">
            <v-card class="mt-4">
              <v-card-title>Report Cards</v-card-title>
              <v-card-text>
                <v-select
                  v-model="selectedClass"
                  :items="homeroomClasses"
                  item-text="class_name"
                  item-value="id"
                  label="Select Class (Homeroom)"
                  @change="loadReportCards"
                ></v-select>
                <v-data-table
                  v-if="reportCards.length"
                  :headers="reportHeaders"
                  :items="reportCards"
                  class="elevation-1"
                >
                  <template v-slot:item.actions="{ item }">
                    <v-btn small color="primary" @click="openReportDialog(item)">Edit Notes</v-btn>
                  </template>
                </v-data-table>
              </v-card-text>
            </v-card>
          </v-tab-item>
        </v-tabs-items>
      </v-container>
    </v-main>

    <!-- Report Dialog -->
    <v-dialog v-model="reportDialog" max-width="600px">
      <v-card>
        <v-card-title>Edit Report Card Notes</v-card-title>
        <v-card-text>
          <v-textarea
            v-model="reportForm.homeroom_teacher_notes"
            label="Homeroom Teacher Notes"
          ></v-textarea>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="reportDialog = false">Cancel</v-btn>
          <v-btn color="primary" @click="saveReportNotes">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-app>
</template>

<script>
import axios from '../api/axios'

export default {
  name: 'GuruDashboard',
  data() {
    return {
      drawer: true,
      activeTab: 'attendance',
      loading: false,
      teacherClasses: [],
      teacherSubjects: [],
      homeroomClasses: [],
      selectedClass: null,
      selectedSubject: null,
      attendanceDate: new Date().toISOString().substr(0, 10),
      gradeDate: new Date().toISOString().substr(0, 10),
      assessmentType: '',
      attendanceStudents: [],
      gradeStudents: [],
      reportCards: [],
      reportDialog: false,
      reportForm: {},
      attendanceHeaders: [
        { text: 'NIS', value: 'nis' },
        { text: 'Name', value: 'full_name' },
        { text: 'Status', value: 'status' },
        { text: 'Notes', value: 'notes' }
      ],
      gradeHeaders: [
        { text: 'NIS', value: 'nis' },
        { text: 'Name', value: 'full_name' },
        { text: 'Score', value: 'score' }
      ],
      reportHeaders: [
        { text: 'Student Name', value: 'student_name' },
        { text: 'Semester', value: 'semester' },
        { text: 'Academic Year', value: 'academic_year' },
        { text: 'Actions', value: 'actions', sortable: false }
      ]
    }
  },
  computed: {
    currentUser() {
      return JSON.parse(localStorage.getItem('user'))
    }
  },
  mounted() {
    this.loadTeacherData()
  },
  methods: {
    async loadTeacherData() {
      try {
        // Load teacher subjects and classes
        const teacherSubjectsRes = await axios.get(`/teacher_subjects?filter=teacher_id,eq,${this.currentUser.id}`)
        this.teacherSubjects = teacherSubjectsRes.data.teacher_subjects.records

        // Get unique classes from teacher subjects
        const classIds = [...new Set(this.teacherSubjects.map(ts => ts.class_id))]
        const classesRes = await axios.get(`/classes?filter=id,in,${classIds.join(',')}`)
        this.teacherClasses = classesRes.data.classes.records

        // Load homeroom classes
        const homeroomRes = await axios.get(`/classes?filter=homeroom_teacher_id,eq,${this.currentUser.id}`)
        this.homeroomClasses = homeroomRes.data.classes.records
      } catch (error) {
        console.error('Error loading teacher data:', error)
      }
    },
    async loadStudentsForAttendance() {
      if (!this.selectedClass) return
      try {
        const studentsRes = await axios.get(`/students?filter=class_id,eq,${this.selectedClass}`)
        this.attendanceStudents = studentsRes.data.students.records.map(student => ({
          ...student,
          status: 'hadir',
          notes: ''
        }))
      } catch (error) {
        console.error('Error loading students:', error)
      }
    },
    async loadStudentsForGrades() {
      if (!this.selectedClass) return
      try {
        const studentsRes = await axios.get(`/students?filter=class_id,eq,${this.selectedClass}`)
        this.gradeStudents = studentsRes.data.students.records.map(student => ({
          ...student,
          score: null
        }))
      } catch (error) {
        console.error('Error loading students:', error)
      }
    },
    async saveAttendance() {
      try {
        const attendanceData = this.attendanceStudents.map(student => ({
          student_id: student.id,
          subject_id: this.selectedSubject,
          date: this.attendanceDate,
          status: student.status,
          notes: student.notes
        }))

        for (const attendance of attendanceData) {
          await axios.post('/attendances', attendance)
        }

        alert('Attendance saved successfully!')
        this.attendanceStudents = []
      } catch (error) {
        console.error('Error saving attendance:', error)
        alert('Error saving attendance')
      }
    },
    async saveGrades() {
      try {
        const gradeData = this.gradeStudents
          .filter(student => student.score !== null && student.score !== '')
          .map(student => ({
            student_id: student.id,
            subject_id: this.selectedSubject,
            assessment_type: this.assessmentType,
            score: parseFloat(student.score),
            grade_date: this.gradeDate
          }))

        for (const grade of gradeData) {
          await axios.post('/grades', grade)
        }

        alert('Grades saved successfully!')
        this.gradeStudents = []
      } catch (error) {
        console.error('Error saving grades:', error)
        alert('Error saving grades')
      }
    },
    async loadReportCards() {
      if (!this.selectedClass) return
      try {
        const studentsRes = await axios.get(`/students?filter=class_id,eq,${this.selectedClass}`)
        const studentIds = studentsRes.data.students.records.map(s => s.id)
        const reportsRes = await axios.get(`/report_cards?filter=student_id,in,${studentIds.join(',')}`)
        this.reportCards = reportsRes.data.report_cards.records.map(report => ({
          ...report,
          student_name: studentsRes.data.students.records.find(s => s.id === report.student_id)?.full_name
        }))
      } catch (error) {
        console.error('Error loading report cards:', error)
      }
    },
    openReportDialog(report) {
      this.reportForm = { ...report }
      this.reportDialog = true
    },
    async saveReportNotes() {
      try {
        await axios.put(`/report_cards/${this.reportForm.id}`, {
          homeroom_teacher_notes: this.reportForm.homeroom_teacher_notes
        })
        this.reportDialog = false
        this.loadReportCards()
        alert('Report notes updated successfully!')
      } catch (error) {
        console.error('Error saving report notes:', error)
        alert('Error saving report notes')
      }
    },
    logout() {
      localStorage.removeItem('user')
      this.$router.push('/')
    }
  }
}
</script>
