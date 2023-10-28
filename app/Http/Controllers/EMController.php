<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Project;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class EMController extends Controller
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
    
        $student = new Student();
        $student->username = $request->input('username');
        $student->email = $request->input('email');
        $student->password = Hash::make($request->input('password'));
    
        if ($student->save()) {
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

        $student = Student::where('email', $email)->first();

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
        $student = Student::find($id);
    
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }
    
        $validator = Validator::make($request->all(), [
            'new_username' => 'sometimes|string|max:50',
            'new_password' => 'sometimes|string|max:50',
            'new_email' => 'sometimes|email|unique:students,email,' . $id,
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        if (!$request->has('new_username') && !$request->has('new_password') && !$request->has('new_email')) {
            return response()->json(['error' => 'At least one of the fields (new_username, new_password, new_email) is required'], 400);
        }
    
        if ($request->has('new_username')) {
            $student->username = $request->input('new_username');
        }
    
        if ($request->has('new_password')) {
            $student->password = Hash::make($request->input('new_password'));
        }
    
        if ($request->has('new_email')) {
            $student->email = $request->input('new_email');
        }
    
        $student->save();
    
        return response()->json(['message' => 'successful'], 200);
    }
    



public function delete_Student($id)
{
    $student = Student::find($id);

    if (!$student) {
        return response()->json(['error' => 'Student not found'], 404);
    }
    $student->delete();

    return response()->json(['message' => 'successful'], 200);
}





public function get_student($id)
{
    $student = Student::find($id);

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
    $students = Student::paginate($perPage);

    return response()->json($students, 200);
}


public function create_user_and_project(Request $request)
{
    DB::beginTransaction();

    try {
        $this->validate($request, [
            'user' => 'required|array',
            'user.name' => 'required|string',
            'user.email' => 'required|email|unique:students,email',
            'user.password' => 'required|string',
            'projects' => 'required|array',
            'projects.*.project_name' => 'required|string',
        ]);

        $userData = $request->input('user');
        $projectsData = $request->input('projects');

        // Create the user
        $user = new Student;
        $user->username = $userData['name'];
        $user->email = $userData['email'];
        $user->password = Hash::make($userData['password']);
        $user->save();

        // Retrieve the user ID
        $user_id = $user->id;

        // Create and associate projects with the user
        foreach ($projectsData as $projectData) {
            $project = new Project;
            $project->project_name = $projectData['project_name'];
            $project->student_id = $user_id; // Set the user_id as student_id
            $project->save();
        }

        DB::commit();

        return response()->json(['message' => 'User and Projects created successfully']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'An error occurred while creating user and projects' . $e], 500);
    }
}

    
}
