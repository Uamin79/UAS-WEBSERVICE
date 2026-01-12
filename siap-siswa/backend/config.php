<?php
$config = new Config([
    'username' => 'root',
    'password' => '',
    'database' => 'db_siap_siswa',
    'address' => 'localhost',
    'driver' => 'mysql',
    'tables' => 'users,admins,teachers,classes,students,parents,student_parent_relations,subjects,teacher_subjects,attendances,grades,report_cards',
    'middlewares' => 'cors,authorization',
    'authorization.tableHandler' => function ($operation, $tableName) {
        return true; // Allow all for now, implement role-based later
    },
]);
?>
