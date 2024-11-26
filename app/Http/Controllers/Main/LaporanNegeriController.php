<?php

namespace App\Http\Controllers\Main;

use App\Notifications\LaporanNotification;
use App\Http\Controllers\Controller;
use App\Models\PerguruanTinggiNegeri;
use App\Models\LaporanPTN;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaporanNegeriController extends Controller
{
    public function index()
    {
        $laporan_list = PerguruanTinggiNegeri::all();
        return view('laporan-ptn.index', compact('laporan_list'));
    }

    public function create($uuid)
    {
        try {
            $ptn = PerguruanTinggiNegeri::where('uuid', $uuid)->firstOrFail();
            return view('laporan-ptn.create', compact('ptn'));
        } catch (\Exception $e) {
            return redirect()->route('laporan-ptn.index')
                ->with('error', 'Perguruan Tinggi Negeri Tidak Ditemukan.');
        }
    }
        
    public function store(Request $request)
    {
        $request->validate([
            'ptn_id' => 'required|exists:ptn,id',
            'tanggal_kegiatan' => 'required|date',
            'tempat_kegiatan' => 'required|string|max:255',
            'jenis_kegiatan' => 'required|in:Rapat/Audiensi,Visitasi,Monitoring & Evaluasi,Aduan/Laporan,Teguran/Sanksi',
            'dokumen_notula' => 'required|file|mimes:pdf|max:2048',
            'dokumen_undangan' => 'required|file|mimes:pdf|max:2048',
            'resume' => 'required|string|max:500',
            'createdbyuser' => 'required|string|max:255',
        ], [
            'ptn_id.required' => 'ID Perguruan Tinggi diperlukan.',
            'ptn_id.exists' => 'Perguruan Tinggi tidak valid.',
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
            // Simpan file di folder public yang bisa diakses
            $notula_path = $request->file('dokumen_notula')->store('notula', 'public');
            $undangan_path = $request->file('dokumen_undangan')->store('undangan', 'public');
    
            $laporan = new LaporanPTN();
            $laporan->ptn_id = $request->ptn_id;
            $laporan->user_id = Auth::id();
            $laporan->tanggal_kegiatan = $request->tanggal_kegiatan;
            $laporan->tempat_kegiatan = $request->tempat_kegiatan;
            $laporan->jenis_kegiatan = $request->jenis_kegiatan;
            $laporan->dokumen_notula = $notula_path; // Simpan path relatif
            $laporan->dokumen_undangan = $undangan_path; // Simpan path relatif
            $laporan->resume = $request->resume;
            $laporan->status = 'final';
            $laporan->createdbyuser = $request->createdbyuser;
            $laporan->save();

            // Tambahkan setelah laporan berhasil disimpan
            $data = [
                'title' => 'Laporan Baru Dibuat',
                'message' => 'Laporan baru telah ditambahkan oleh ' . Auth::user()->name,
                'url' => route('laporan-ptn.show', $laporan->ptn_id),
            ];
            Auth::user()->notify(new LaporanNotification($data));
    
            return redirect()->route('laporan-ptn.index')
                ->with('success', 'Kegiatan berhasil ditambahkan!');
    
        } catch (\Exception $e) {
            // Hapus file jika gagal
            if (isset($notula_path)) Storage::disk('public')->delete($notula_path);
            if (isset($undangan_path)) Storage::disk('public')->delete($undangan_path);
    
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }

    public function show(Request $request, $uuid)
    {
        $ptn = PerguruanTinggiNegeri::where('uuid', $uuid)->firstOrFail();
    
        // Query laporan berdasarkan ptn_id
        $laporan = LaporanPTN::where('ptn_id', $ptn->id);
    
        // Filter berdasarkan jenis_kegiatan
        if ($request->filled('jenis_kegiatan')) {
            $laporan->where('jenis_kegiatan', $request->jenis_kegiatan);
        }
    
        // Filter berdasarkan tahun
        if ($request->filled('filter_year')) {
            $laporan->whereYear('tanggal_kegiatan', $request->filter_year);
        }
    
        // Filter berdasarkan bulan
        if ($request->filled('filter_month')) {
            $laporan->whereMonth('tanggal_kegiatan', $request->filter_month);
        }
    
        // Dapatkan semua laporan dan tambahkan properti canEdit
        $laporan = $laporan->get()->map(function ($item) {
            // Tombol edit/hapus tersedia jika:
            // - Pengguna adalah admin, atau
            // - Pengguna adalah pemilik laporan dan laporan dibuat dalam waktu 3 hari terakhir
            $item->canEdit = Auth::user()->akses === 'Admin' ||
                            (Auth::id() === $item->user_id &&
                            $item->created_at->greaterThanOrEqualTo(Carbon::now()->subDays(3)));
            return $item;
        });
    
        // Kirim data ke view
        return view('laporan-ptn.show', compact('ptn', 'laporan'));
    }    
    
    public function edit($uuid)
    {
        $laporan = LaporanPTN::where('uuid', $uuid)->firstOrFail();
        $ptn = PerguruanTinggiNegeri::findOrFail($laporan->ptn_id);
        return view('laporan-ptn.edit', compact('laporan', 'ptn'));
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
        $laporan = LaporanPTN::where('uuid', $uuid)->first();
    
        if (!$laporan) {
            return redirect()->back()->with('error', 'Laporan tidak ditemukan.');
        }
    
        // Pastikan relasi ptn ada
        if (!$laporan->ptn) {
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
                'url' => route('laporan-ptn.show', $laporan->ptn->uuid),
            ];
            Auth::user()->notify(new LaporanNotification($data));
    
            // Redirect ke halaman show dengan pesan sukses
            return redirect()->route('laporan-ptn.show', $laporan->ptn->uuid)
                ->with('success', 'Kegiatan berhasil diperbarui!');
        }
    
        // Jika penyimpanan gagal
        return redirect()->back()
            ->withInput()
            ->with('error', 'Gagal menyimpan perubahan. Silakan coba lagi.');
    }           

    public function destroy($uuid)
    {
        $laporan = LaporanPTN::where('uuid', $uuid)->firstOrFail();
    
        // Delete the files if they exist
        if (Storage::disk('public')->exists($laporan->dokumen_notula)) {
            Storage::disk('public')->delete($laporan->dokumen_notula);
        }
        if (Storage::disk('public')->exists($laporan->dokumen_undangan)) {
            Storage::disk('public')->delete($laporan->dokumen_undangan);
        }
    
        $laporan->delete();
    
        return redirect()->route('laporan-ptn.index')
            ->with('success', 'Kegiatan berhasil dihapus!');
    }    

    public function printToPdf(Request $request, $uuid)
    {
        $ptn = PerguruanTinggiNegeri::where('uuid', $uuid)->firstOrFail();
        $laporan = LaporanPTN::where('ptn_id', $ptn->id);
    
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
    
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan-ptn.pdf', compact('ptn', 'laporan'))
            ->setPaper('a4', 'portrait');
    
        return $pdf->download('Timeline_Kegiatan_' . $ptn->nama_pt . '.pdf');
    }    
}
