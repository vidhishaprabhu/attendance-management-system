<?php

namespace App\Exports;

use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;

class AttendanceExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $month;
    protected $year;
    public function __construct($month,$year){
        $this->month=$month;
        $this->year=$year;

    }
    public function collection()
    {
        return Attendance::where('user_id',Auth::id())
        ->whereMonth('date',$this->month)
        ->whereYear('date',$this->year)
        ->get(['date','status']);

    }
    public function heading(): array{
        return ["Date","Status"];
    }
}
