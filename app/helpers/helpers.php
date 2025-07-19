<?php

use Illuminate\Support\Facades\Auth;

function table_date($datetime)
{
    if (!$datetime) {
        return 'N/A';
    }
    try {
        $date = DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $datetime);
        if (!$date) {
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
        }

        return $date instanceof DateTime ? $date->format('M d, Y') : 'Invalid Date';
    } catch (Exception $e) {
        return 'Error';
    }
}

function end_url()
{
    return url('/api') . '/';
}

function user_roles($role_no)
{
    $roles = [
        1 => 'Super Admin',
        2 => 'Admin',
        3 => 'Staff',
        4 => 'Student',
    ];

    return $roles[$role_no] ?? false;
}

function user_role_no($role_name)
{
    $roles = [
        'Super Admin' => 1,
        'Admin'       => 2,
        'Staff'       => 3,
        'Student'     => 4,
    ];

    return $roles[$role_name] ?? false;
}

function auth_users()
{
    return [1, 2];
}

function active_users()
{
    return [1];
}

function view_permission($page_name)
{
    if (!Auth::check()) {
        return false;
    }

    $user_role = Auth::user()->role;

    switch ($user_role) {
        case 'Super Admin':
            return true;

        case 'Admin':
            $admin_pages = [
                'dashboard',
                'user_management',
                'staff_management',
                'student_management',
                'settings',
            ];
            return in_array($page_name, $admin_pages);

        case 'Staff':
            $staff_pages = [
                'dashboard',
                'my_profile',
                'manage_courses',
                'manage_attendance',
                'view_students',
            ];
            return in_array($page_name, $staff_pages);

        case 'Student':
            $student_pages = [
                'dashboard',
                'my_profile',
                'my_courses',
                'my_grades',
                'view_attendance',
            ];
            return in_array($page_name, $student_pages);

        default:
            return false;
    }
}