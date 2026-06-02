<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReportResource;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'judul_laporan' => 'required|string|max:255',
            'lokasi_fasilitas' => 'required|string|max:255',
            'deskripsi_kerusakan' => 'required|string',
            'foto_bukti' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $report = new Report();
        $report->user_id = $request->user()->id;
        $report->judul_laporan = $request->judul_laporan;
        $report->lokasi_fasilitas = $request->lokasi_fasilitas;
        $report->deskripsi_kerusakan = $request->deskripsi_kerusakan;
        $report->status = 'pending';

        if ($request->hasFile('foto_bukti')) {
            $path = $request->file('foto_bukti')->store('reports', 'public');
            $report->foto_bukti = $path;
        }

        $report->save();

        return response()->json([
            'success' => true,
            'message' => 'Report created successfully',
            'data' => new ReportResource($report->load('user')),
        ], 201);
    }

    public function index(Request $request)
    {
        $query = Report::query();

        if ($request->has('q') && $request->q) {
            $search = $request->q;
            $query->where('judul_laporan', 'like', "%{$search}%")
                  ->orWhere('lokasi_fasilitas', 'like', "%{$search}%");
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $perPage = $request->input('per_page', 15);
        $reports = $query->withoutTrashed()
                         ->with('user')
                         ->orderByDesc('created_at')
                         ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Reports retrieved',
            'data' => ReportResource::collection($reports),
            'pagination' => [
                'total' => $reports->total(),
                'per_page' => $reports->perPage(),
                'current_page' => $reports->currentPage(),
                'last_page' => $reports->lastPage(),
                'from' => $reports->firstItem(),
                'to' => $reports->lastItem(),
            ],
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', Rule::in(['pending', 'diproses', 'selesai'])],
        ]);

        $report = Report::findOrFail($id);
        $report->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Report status updated',
            'data' => new ReportResource($report->load('user')),
        ]);
    }
}
