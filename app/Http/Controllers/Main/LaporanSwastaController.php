<?php

namespace App\Http\Controllers\Main;

use App\Notifications\LaporanNotification;
use App\Http\Controllers\Controller;
use App\Models\PerguruanTinggiSwasta;
use App\Models\LaporanPTS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaporanSwastaController extends Controller
{
    public function index()
    {
        $laporan_list = PerguruanTinggiSwasta::all();
        return view('laporan-pts.index', compact('laporan_list'));
    }

    public function create($uuid)
    {
        try {
            $pts = PerguruanTinggiSwasta::where('uuid', $uuid)->firstOrFail();
            return view('laporan-pts.create', compact('pts'));
        } catch (\Exception $e) {
            return redirect()->route('laporan-pts.index')
                ->with('error', 'Perguruan Tinggi Swasta Tidak Ditemukan.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'pts_id' => 'required|exists:pts,id',
            'tanggal_kegiatan' => 'required|date',
            'tempat_kegiatan' => 'required|string|max:255',
            'jenis_kegiatan' => 'required|in:Rapat/Audiensi,Visitasi,Monitoring & Evaluasi,Aduan/Laporan,Teguran/Sanksi',
            'dokumen_notula' => 'required|file|mimes:pdf|max:2048',
            'dokumen_undangan' => 'required|file|mimes:pdf|max:2048',
            'resume' => 'required|string|max:500',
            'createdbyuser' => 'required|string|max:255',
        ], [
            'pts_id.required' => 'ID Perguruan Tinggi diperlukan.',
            'pts_id.exists' => 'Perguruan Tinggi tidak valid.',
            'tanggal_kegiatan.required' => 'Tanggal kegiatan harus diisi.',
            'tanggal_kegiatan.date' => 'Format tanggal tidak valid.',
            'tempat_kegiatan.required' => 'Tempat kegiatan harus diisi.',
            'tempat_kegiatan.max' => 'Tempat kegiatan maksimal 255 karakter.',
            'jenis_kegiatan.required' => 'Jenis kegiatan harus dipilih.',
            'jenis_kegiatan.in' => 'Jenis kegiatan tidak valid.',
            'dokumen_notula.required' => 'Dokumen notula harus diunggah.',
            'dokumen_notula.mimes' => 'Dokumen notula harus berformat PDF.',
            'dokumen_notula.max' => 'Ukuran dokumen notula maksimal 2MB.',
            'dokumen_undangan.required' => 'Dokumen undangan harus diunggah.',
            'dokumen_undangan.mimes' => 'Dokumen undangan harus berformat PDF.',
            'dokumen_undangan.max' => 'Ukuran dokumen undangan maksimal 2MB.',
            'resume.required' => 'Ringkasan harus diisi.',
            'resume.max' => 'Ringkasan maksimal 500 karakter.',
            'createdbyuser.required' => 'Nama pembuat harus diisi.',
            'createdbyuser.max' => 'Nama pembuat maksimal 255 karakter.',
        ]);
    
        try {
            $notula_path = $request->file('dokumen_notula')->store('notula', 'public');
            $undangan_path = $request->file('dokumen_undangan')->store('undangan', 'public');

            $laporan = new LaporanPTS();
            $laporan->pts_id = $request->pts_id;
            $laporan->user_id = Auth::id();
            $laporan->tanggal_kegiatan = $request->tanggal_kegiatan;
            $laporan->tempat_kegiatan = $request->tempat_kegiatan;
            $laporan->jenis_kegiatan = $request->jenis_kegiatan;
            $laporan->dokumen_notula = $notula_path;
            $laporan->dokumen_undangan = $undangan_path;
            $laporan->resume = $request->resume;
            $laporan->status = 'final';
            $laporan->createdbyuser = $request->createdbyuser;
            $laporan->save();

            $data = [
                'title' => 'Laporan Baru Dibuat',
                'message' => 'Laporan baru telah ditambahkan oleh ' . Auth::user()->name,
                'url' => route('laporan-pts.show', $laporan->uuid),
            ];
            Auth::user()->notify(new LaporanNotification($data));

            return redirect()->route('laporan-pts.index')
                ->with('success', 'Kegiatan berhasil ditambahkan!');
        } catch (\Exception $e) {
            if (isset($notula_path)) Storage::disk('public')->delete($notula_path);
            if (isset($undangan_path)) Storage::disk('public')->delete($undangan_path);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }

    public function show(Request $request, $uuid)
    {
        $pts = PerguruanTinggiSwasta::where('uuid', $uuid)->firstOrFail();

        $laporan = LaporanPTS::where('pts_id', $pts->id);

        if ($request->filled('jenis_kegiatan')) {
            $laporan->where('jenis_kegiatan', $request->jenis_kegiatan);
        }

        if ($request->filled('filter_year')) {
            $laporan->whereYear('tanggal_kegiatan', $request->filter_year);
        }

        if ($request->filled('filter_month')) {
            $laporan->whereMonth('tanggal_kegiatan', $request->filter_month);
        }

        $laporan = $laporan->get()->map(function ($item) {
            $item->canEdit = Auth::user()->akses === 'Admin' ||
                (Auth::id() === $item->user_id &&
                    $item->created_at->greaterThanOrEqualTo(Carbon::now()->subDays(3)));
            return $item;
        });

        return view('laporan-pts.show', compact('pts', 'laporan'));
    }

    public function edit($uuid)
    {
        $laporan = LaporanPTS::where('uuid', $uuid)->firstOrFail();
        $pts = PerguruanTinggiSwasta::findOrFail($laporan->pts_id);
        return view('laporan-pts.edit', compact('laporan', 'pts'));
    } 

    public function update(Request $request, $uuid)
    {
        $request->validate([
            'tanggal_kegiatan' => 'required|date',
            'tempat_kegiatan' => 'required|string|max:255',
            'jenis_kegiatan' => 'required|in:Rapat/Audiensi,Visitasi,Monitoring & Evaluasi,Aduan/Laporan,Teguran/Sanksi',
            'dokumen_notula' => 'nullable|file|mimes:pdf|max:2048',
            'dokumen_undangan' => 'nullable|file|mimes:pdf|max:2048',
            'resume' => 'required|string|max:500',
        ]);
    
        // Cari laporan berdasarkan UUID
        $laporan = LaporanPTS::where('uuid', $uuid)->first();
    
        if (!$laporan) {
            return redirect()->back()->with('error', 'Laporan tidak ditemukan.');
        }
    
        // Pastikan relasi ptn ada
        if (!$laporan->pts) {
            return redirect()->back()->with('error', 'Data Perguruan Tinggi Negeri terkait tidak ditemukan.');
        }
    
        // Update data laporan
        $laporan->tanggal_kegiatan = $request->tanggal_kegiatan;
        $laporan->tempat_kegiatan = $request->tempat_kegiatan;
        $laporan->jenis_kegiatan = $request->jenis_kegiatan;
        $laporan->resume = $request->resume;
    
        // Update dokumen_notula jika ada
        if ($request->hasFile('dokumen_notula')) {
            if (Storage::disk('public')->exists($laporan->dokumen_notula)) {
                Storage::disk('public')->delete($laporan->dokumen_notula);
            }
            $laporan->dokumen_notula = $request->file('dokumen_notula')->store('notula', 'public');
        }
    
        // Update dokumen_undangan jika ada
        if ($request->hasFile('dokumen_undangan')) {
            if (Storage::disk('public')->exists($laporan->dokumen_undangan)) {
                Storage::disk('public')->delete($laporan->dokumen_undangan);
            }
            $laporan->dokumen_undangan = $request->file('dokumen_undangan')->store('undangan', 'public');
        }
    
        if ($laporan->save()) {
            // Kirim notifikasi
            $data = [
                'title' => 'Laporan Diperbarui',
                'message' => 'Laporan telah diperbarui oleh ' . Auth::user()->name,
                'url' => route('laporan-pts.show', $laporan->pts->uuid),
            ];
            Auth::user()->notify(new LaporanNotification($data));
    
            // Redirect ke halaman show dengan pesan sukses
            return redirect()->route('laporan-pts.show', $laporan->pts->uuid)
                ->with('success', 'Kegiatan berhasil diperbarui!');
        }
    
        // Jika penyimpanan gagal
        return redirect()->back()
            ->withInput()
            ->with('error', 'Gagal menyimpan perubahan. Silakan coba lagi.');
    }

    public function destroy($uuid)
    {
        try {
            $laporan = LaporanPTS::where('uuid', $uuid)->firstOrFail();

            if (Storage::disk('public')->exists($laporan->dokumen_notula)) {
                Storage::disk('public')->delete($laporan->dokumen_notula);
            }
            if (Storage::disk('public')->exists($laporan->dokumen_undangan)) {
                Storage::disk('public')->delete($laporan->dokumen_undangan);
            }

            $laporan->delete();

            return redirect()->route('laporan-pts.index')
                ->with('success', 'Kegiatan berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus kegiatan. Silakan coba lagi.');
        }
    }

    public function printToPdf(Request $request, $pts_id)
    {
        $pts = PerguruanTinggiSwasta::findOrFail($pts_id);
        $laporan = LaporanPTS::where('pts_id', $pts_id);
    
        // Apply filters
        if ($request->filled('jenis_kegiatan')) {
            $laporan->where('jenis_kegiatan', $request->jenis_kegiatan);
        }
        if ($request->filled('filter_year')) {
            $laporan->whereYear('tanggal_kegiatan', $request->filter_year);
        }
        if ($request->filled('filter_month')) {
            $laporan->whereMonth('tanggal_kegiatan', $request->filter_month);
        }
    
        $laporan = $laporan->get();
    
        // Load the PDF view with filtered data
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan-pts.pdf', compact('pts', 'laporan'))
            ->setPaper('a4', 'portrait');
    
        return $pdf->download('Timeline_Kegiatan_' . $pts->nama_pt . '.pdf');
    }
}
