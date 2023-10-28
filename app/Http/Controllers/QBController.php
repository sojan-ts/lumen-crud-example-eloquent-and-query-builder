<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class QBController extends Controller
{

    public function register_student(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'email' => 'required|email|unique:students',
            'password' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
    
        $student = [
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ];
    
        $inserted = DB::table('students')->insert($student);
    
        if ($inserted) {
            return response()->json(['message' => 'Successful'], 201);
        } else {
            return response()->json(['error' => 'Failed to insert student data'], 500);
        }
    }



    public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    $email = $request->input('email');
    $password = $request->input('password');

    $student = DB::table('students')->where('email', $email)->first();

    if (!$student) {
        return response()->json(['error' => 'Invalid email or password'], 401);
    }

    if (Hash::check($password, $student->password)) {
        $token = $this->generateToken();
        return response()->json(['message' => 'Login successful', 'access_token' => $token], 200);
    } else {
        return response()->json(['error' => 'Invalid email or password'], 401);
    }
}
private function generateToken()
{
    return "jgfjhgfjvgdj675757gjfkjgjlg7868768fhkjbdkvhkdjlfvh6f8v";
}




   public function update_student(Request $request, $id)
{
    $student = DB::table('students')->where('id', $id)->first();

    if (!$student) {
        return response()->json(['error' => 'Student not found'], 404);
    }

    $updateData = [];

    $validator = Validator::make($request->all(), [
        'new_username' => 'sometimes|string|max:50',
        'new_password' => 'sometimes|string|max:50',
        'new_email' => 'sometimes|email|unique:students,email,' . $id,
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    if ($request->has('new_username')) {
        $updateData['username'] = $request->input('new_username');
    }

    if ($request->has('new_password')) {
        $updateData['password'] = Hash::make($request->input('new_password'));
    }

    if ($request->has('new_email')) {
        $updateData['email'] = $request->input('new_email');
    }

    DB::table('students')
        ->where('id', $id)
        ->update($updateData);

    return response()->json(['message' => 'successful'], 200);
}
    



public function delete_Student($id)
{
    $student = DB::table('students')->where('id', $id)->first();

    if (!$student) {
        return response()->json(['error' => 'Student not found'], 404);
    }

    DB::table('students')->where('id', $id)->delete();

    return response()->json(['message' => 'successful'], 200);
}




public function get_student($id)
{
    $student = DB::table('students')->where('id', $id)->first();

    if (!$student) {
        return response()->json(['error' => 'Student not found'], 404);
    }

    $response = [
        'message' => 'successful',
        'student' => [
            'id' => $student->id,
            'username' => $student->username,
            'email' => $student->email,
        ]
    ];

    return response()->json($response, 200);
}




public function list_students(Request $request)
{
    $perPage = $request->input('per_page', 10);

    $students = DB::table('students')
        ->select('id', 'username', 'email', 'created_at')
        ->paginate($perPage);

    return response()->json($students, 200);
}


public function create_user_and_project(Request $request)
{
    DB::beginTransaction();

    try {
        $validator = Validator::make($request->all(), [
            'user' => 'required|array',
            'user.name' => 'required|string',
            'user.email' => 'required|email|unique:students,email',
            'user.password' => 'required|string',
            'projects' => 'required|array',
            'projects.*.project_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $userData = $request->input('user');
        $projectsData = $request->input('projects');

        $user_id = DB::table('students')->insertGetId([
            'username' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
        ]);

        $projects = [];
        foreach ($projectsData as $projectData) {
            $projects[] = [
                'project_name' => $projectData['project_name'],
                'student_id' => $user_id,
            ];
        }

        DB::table('projects')->insert($projects);

        DB::commit();

        return response()->json(['message' => 'User and Projects created successfully']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'An error occurred while creating user and projects: ' . $e->getMessage()], 500);
    }
}

    
}
