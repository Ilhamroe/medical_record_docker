<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateRequest;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AuthenticationController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();

        $imagePath = $this->handleImageUpload($request, 'image');

        $userData = [
            'name' => $validatedData['name'],
            'nrp' => $validatedData['nrp'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
        ];

        if ($imagePath) {
            $userData['image'] = $imagePath;
        }

        if (isset($validatedData['gender'])) {
            $userData['gender'] = $validatedData['gender'];
        }

        if (isset($validatedData['birth'])) {
            $userData['birth'] = date('Y-m-d', strtotime($validatedData['birth']));
        }

        if (isset($validatedData['number'])) {
            $userData['number'] = $validatedData['number'];
        }

        if (isset($validatedData['height'])) {
            $userData['height'] = $validatedData['height'];
        }

        if (isset($validatedData['weight'])) {
            $userData['weight'] = $validatedData['weight'];
        }

        if (isset($validatedData['description'])) {
            $userData['description'] = $validatedData['description'];
        }

        $user = User::create($userData);
        $token = $user->createToken('medicalRecords')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function login(LoginRequest $request)
    {
        $request->validated();

        $user = User::whereEmail($request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'Invalid Credentials'
            ], 422);
        }

        $token = $user->createToken('medicalRecords')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token,
        ], 200);

        // $credentials = request()->only(['email', 'password']);
        // if (Auth::guard('api')->attempt($credentials)) {
        //     return response(['message' => 'Invalid Credentials'], 422);
        // }

        // $user = auth()->user();
        // return response()->json([
        //     'user' => $user,
        //     'token' => $user->createToken('medicalRecords')->plainTextToken
        // ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response([
            'message' => 'Logged out successfully'
        ]);
    }

    public function update(UpdateRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validated();

        $imagePath = $this->handleImageUpload($request, 'image', $user->image);

        if (isset($validatedData['name'])) {
            $userData['name'] = $validatedData['name'];
        }

        if (isset($validatedData['nrp'])) {
            $userData['nrp'] = $validatedData['nrp'];

        }
        if (isset($validatedData['email'])) {
            $userData['email'] = $validatedData['email'];
        }

        if (!empty($validatedData['password'])) {
            $userData['password'] = Hash::make($validatedData['password']);
        }

        if (isset($validatedData['role'])) {
            $userData['role'] = $validatedData['role'];
        }

        if ($imagePath) {
            $userData['image'] = $imagePath;
        }

        if (isset($validatedData['gender'])) {
            $userData['gender'] = $validatedData['gender'];
        }

        if (isset($validatedData['birth'])) {
            $userData['birth'] = date('Y-m-d', strtotime($validatedData['birth']));
        }

        if (isset($validatedData['number'])) {
            $userData['number'] = $validatedData['number'];
        }

        if (isset($validatedData['height'])) {
            $userData['height'] = $validatedData['height'];
        }

        if (isset($validatedData['weight'])) {
            $userData['weight'] = $validatedData['weight'];
        }

        if (isset($validatedData['description'])) {
            $userData['description'] = $validatedData['description'];
        }

        $user->update($userData);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ], 200);
    }

    private function handleImageUpload($request, $fieldName, $existingImagePath = null)
    {
        if ($request->hasFile($fieldName)) {
            if ($existingImagePath) {
                Storage::disk('public')->delete($existingImagePath);
            }
            return $request->file($fieldName)->store('images', 'public');
        }
        return $existingImagePath;
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (auth()->user()->role !== 'admin' && auth()->user()->id !== $user->id) {
            return response()->json(['error' => 'You do not have permission to delete this user'], 403);
        }

        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }

        Clinic::where('patient_id', $id)->delete();

        $user->delete();

        return response()->json([
            'message' => 'User and associated clinics deleted successfully'
        ], 200);
    }

    //fungsi getAllData via login
    public function index()
    {
        $user = User::all();
        return response([
            'user' => $user,
        ], 200);
    }

    //fungsi data byId via login
    public function byId($id)
    {
        $user = User::findOrFail($id);
        return response([
            'user' => $user,
        ], 200);
    }
}
