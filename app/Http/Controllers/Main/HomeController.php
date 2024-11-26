<?php

namespace App\Http\Controllers\Main;

use App\Models\User;
use App\Models\PerguruanTinggiNegeri;
use App\Models\PerguruanTinggiSwasta;
use App\Models\LaporanPTN;
use App\Models\LaporanPTS;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        // Menghitung jumlah data pengguna, PTN, dan PTS
        $userCount = User::count();
        $swastaCount = PerguruanTinggiSwasta::count(); // Jumlah PTS
        $negeriCount = PerguruanTinggiNegeri::count(); // Jumlah PTN

        // Menghitung jumlah total laporan (PTN + PTS)
        $laporanPTNCount = LaporanPTN::count();
        $laporanPTSCount = LaporanPTS::count();
        $laporanCount = $laporanPTNCount + $laporanPTSCount;

        // Statistik laporan PTN
        $todayPTNCount = LaporanPTN::whereDate('created_at', Carbon::today())->count();
        $weeklyPTNCount = LaporanPTN::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $monthlyPTNCount = LaporanPTN::whereMonth('created_at', Carbon::now()->month)
                                     ->whereYear('created_at', Carbon::now()->year)
                                     ->count();
        $yearlyPTNCount = LaporanPTN::whereYear('created_at', Carbon::now()->year)->count();

        // Statistik laporan PTS
        $todayPTSCount = LaporanPTS::whereDate('created_at', Carbon::today())->count();
        $weeklyPTSCount = LaporanPTS::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $monthlyPTSCount = LaporanPTS::whereMonth('created_at', Carbon::now()->month)
                                     ->whereYear('created_at', Carbon::now()->year)
                                     ->count();
        $yearlyPTSCount = LaporanPTS::whereYear('created_at', Carbon::now()->year)->count();

        // Statistik total laporan
        $todayCount = $todayPTNCount + $todayPTSCount;
        $weeklyCount = $weeklyPTNCount + $weeklyPTSCount;
        $monthlyCount = $monthlyPTNCount + $monthlyPTSCount;
        $yearlyCount = $yearlyPTNCount + $yearlyPTSCount;

        return view('main.home', compact(
            'userCount',
            'swastaCount',
            'negeriCount',
            'laporanCount',
            'todayCount',
            'weeklyCount',
            'monthlyCount',
            'yearlyCount'
        ));
    }
}
