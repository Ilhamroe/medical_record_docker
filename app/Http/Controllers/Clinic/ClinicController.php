<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClinicRequest;
use App\Http\Resources\ClinicResource;
use App\Models\Clinic;
use App\Models\Histories;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClinicController extends Controller
{
    //for admin 
    public function showDataAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'You do not have permission to see this data'], 403);
        }

        try {
            $clinic = Clinic::with('patient', 'doctor')->get();
            $clinicResources = ClinicResource::collection($clinic);
            return response()->json([
                "succes" => true,
                "message" => "Data Clinic Berhasil Diambil",
                "data" => $clinicResources
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    //for user 
    public function showDataUser()
    {
        if (auth()->user()->role !== 'user') {
            return response()->json(['error' => 'You do not have permission to see this data'], 403);
        }

        try {
            $clinic = Clinic::with('patient', 'doctor')->where('patient_id', Auth::user()->id)->get();
            $mappedData = $clinic->map(function ($dataHistoryUser) {
                return [
                    'id' => $dataHistoryUser->id,
                    'doctor' => $dataHistoryUser->doctor->name,
                    'dated' => $dataHistoryUser->dated,
                    'symptom' => $dataHistoryUser->symptom,
                    'diagnosis' => $dataHistoryUser->diagnosis,
                    'drug' => $dataHistoryUser->drug,
                    'advice' => $dataHistoryUser->advice
                ];
            });

            return response()->json([
                "succes" => true,
                "message" => "Data Clinic Berhasil Diambil",
                "data" => $mappedData
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    //for doctor 
    public function showDataDoctor()
    {
        if (auth()->user()->role !== 'dokter') {
            return response()->json(['error' => 'You do not have permission to see this data'], 403);
        }

        try {
            $clinic = Clinic::with('patient', 'doctor')->where('doctor_id', Auth::user()->id)->get();
            $mappedData = $clinic->map(function ($dataHistoryUser) {
                return [
                    'id' => $dataHistoryUser->id,
                    'name' => $dataHistoryUser->patient->name,
                    'dated' => $dataHistoryUser->dated,
                    'symptom' => $dataHistoryUser->symptom,
                    'diagnosis' => $dataHistoryUser->diagnosis,
                    'drug' => $dataHistoryUser->drug,
                    'advice' => $dataHistoryUser->advice
                ];
            });

            return response()->json([
                "succes" => true,
                "message" => "Data Clinic Berhasil Diambil",
                "data" => $mappedData
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function store(ClinicRequest $request)
    {
        $validatedData = $request->validated();

        Clinic::create([
            'doctor_id' => Auth::user()->id,
            'patient_id' => $validatedData['patient_id'],
            'dated' => $validatedData['dated'],
            'symptom' => $validatedData['symptom'],
            'diagnosis' => $validatedData['diagnosis'],
            'drug' => $validatedData['drug'],
            'advice' => $validatedData['advice'],
        ]);


        return response()->json([
            'message' => 'success insert data',
        ], 201);
    }


    public function update(ClinicRequest $request, $id)
    {
        $clinic = Clinic::findOrFail($id);

        if (auth()->user()->role !== 'dokter') {
            return response()->json(['error' => 'You do not have permission to update this clinic'], 403);
        }

        $validatedData = $request->validated();

        $clinic->update([
            'user_id' => Auth::user()->id,
            'dated' => $validatedData['dated'],
            'symptom' => $validatedData['symptom'],
            'diagnosis' => $validatedData['diagnosis'],
            'drug' => $validatedData['drug'],
            'advice' => $validatedData['advice'],
        ]);

        return response()->json([
            'message' => 'Clinic updated successfully',
            'data' => $clinic
        ], 200);
    }

    public function destroy($id)
    {
        $clinic = Clinic::findOrFail($id);

        if (auth()->user()->role !== 'dokter') {
            return response()->json(['error' => 'You do not have permission to delete this clinic'], 403);
        }

        $clinic->delete();

        return response()->json([
            'message' => 'Clinic deleted successfully',
        ], 200);
    }

}
