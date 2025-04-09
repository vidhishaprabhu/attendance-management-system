<?php

namespace App\Http\Controllers\API;
use App\Models\Leave;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\AttendanceExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    public function index(Request $request){
        return Attendance::with('user')->latest()->get();
    }
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'status' => 'required|boolean',
        ]);

        $user_id = auth()->id(); 

        $existing = Attendance::where('user_id', $user_id)
                ->where('date', $request->date)
                ->first();
        if ($existing) {
            return response()->json(['message' => 'Attendance already marked for this date.'], 409);
        }

        $attendance = Attendance::create([
            'user_id' => $user_id,
            'date' => $request->date,
            'status' => $request->status,
        ]);

        return response()->json($attendance, 201);
    }
    public function exportMonthlyReport(Request $request){
        $month=$request->input('month');
        $year=$request->input('year');

        return Excel::download(new AttendanceExport($month, $year), "attendance_{$month}_{$year}.xlsx");
    }
    public function exportAttendance(Request $request)
    {
        $user = auth()->user(); 
    
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $month = $request->query('month');
        $year = $request->query('year');

        $attendances = Attendance::where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->get();

        $pdf = Pdf::loadView('attendance.report', ['attendances' => $attendances]);
        return $pdf->download("attendance_{$month}_{$year}.pdf");
    }
    public function applyLeave(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'upto_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'required|string|max:255',
        ]);

        Leave::create([
            'user_id' => Auth::id(),
            'from_date' => $request->from_date,
            'upto_date' => $request->upto_date,
            'reason' => $request->reason,
            'status' => 'Pending',
        ]);

        return response()->json(['message' => 'Leave request submitted successfully']);
    }

}
