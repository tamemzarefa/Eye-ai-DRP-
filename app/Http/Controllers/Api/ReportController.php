<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReportRequest;
use App\Http\Resources\ReportResource;
use App\Models\Report;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function store(StoreReportRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $report = Report::create([
            'doctor_id'     => $request->user()->id,
            'patient_name'  => $validated['patient_name'],
            'patient_age'   => $validated['patient_age'],
            'patient_id'    => $validated['patient_id'],
            'classification'=> $validated['classification'],
            'confidence'    => $validated['confidence'],
            'doctor_notes'  => $validated['doctor_notes'] ?? null,
            'feedback'      => $validated['feedback'] ?? null,
            'image_preview' => $validated['image_preview'] ?? null,
            'date'          => $validated['date'],
        ]);

        return response()->json([
            'message' => 'Report saved successfully.',
            'id'      => $report->id,
            'data'    => new ReportResource($report),
        ], JsonResponse::HTTP_CREATED);
    }

    public function show(Report $report): ReportResource
    {
        return new ReportResource($report);
    }
}
