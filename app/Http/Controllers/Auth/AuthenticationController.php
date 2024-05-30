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
            'image' => $imagePath ?? null,
            'gender' => $validatedData['gender'] ?? null,
            'birth' => isset($validatedData['birth']) ? date('Y-m-d', strtotime($validatedData['birth'])) : null,
            'number' => $validatedData['number'] ?? null,
            'height' => $validatedData['height'] ?? null,
            'weight' => $validatedData['weight'] ?? null,
            'description' => $validatedData['description'] ?? null,
        ];

        $user = User::create($userData);
        $token = $user->createToken('medicalRecords')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $validatedData = $request->validated();

        $user = User::whereEmail($validatedData['email'])->first();
        if (!$user || !Hash::check($validatedData['password'], $user->password)) {
            return response([
                'message' => 'Invalid Credentials'
            ], 422);
        }

        $token = $user->createToken('medicalRecords')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response([
            'message' => 'Logged out successfully'
        ], 200);
    }

    public function update(UpdateRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $validatedData = $request->validated();
        $imagePath = $this->handleImageUpload($request, 'image', $user->image);

        $userData = [
            'name' => $validatedData['name'] ?? $user->name,
            'nrp' => $validatedData['nrp'] ?? $user->nrp,
            'email' => $validatedData['email'] ?? $user->email,
            'password' => isset($validatedData['password']) ? Hash::make($validatedData['password']) : $user->password,
            'role' => $validatedData['role'] ?? $user->role,
            'image' => $imagePath ?? $user->image,
            'gender' => $validatedData['gender'] ?? $user->gender,
            'birth' => isset($validatedData['birth']) ? date('Y-m-d', strtotime($validatedData['birth'])) : $user->birth,
            'number' => $validatedData['number'] ?? $user->number,
            'height' => $validatedData['height'] ?? $user->height,
            'weight' => $validatedData['weight'] ?? $user->weight,
            'description' => $validatedData['description'] ?? $user->description,
        ];

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

    public function index()
    {
        $users = User::all();
        return response([
            'users' => $users,
        ], 200);
    }

    public function byId($id)
    {
        $user = User::findOrFail($id);
        return response([
            'user' => $user,
        ], 200);
    }
}