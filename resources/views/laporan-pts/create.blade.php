@extends('layouts.layout_main')

@section('title', 'Tambah Kegiatan')

@section('content')
<style>
    .gradient-header {
        background: linear-gradient(120deg, #007bff, #0056b3);
        color: #ffffff;
        text-align: center;
        padding: 15px;
        border-radius: 15px 15px 0 0;
        font-size: 18px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        overflow: hidden;
    }
</style>
<section class="section">
    <div class="section-header">
        <h1>Tambah Kegiatan</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('home') }}">Beranda</a></div>
            <div class="breadcrumb-item"><a href="{{ route('laporan-pts.index') }}">Laporan</a></div>
            <div class="breadcrumb-item">Tambah Kegiatan</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header gradient-header">
                        <h4>Form Tambah Kegiatan</h4>
                    </div>
                    <div class="card-body">
                        @if(session('error'))
                        <div class="alert alert-danger alert-dismissible show fade">
                            <div class="alert-body">
                                <button class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                                {{ session('error') }}
                            </div>
                        </div>
                        @endif

                        <form action="{{ route('laporan-pts.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="pts_id" value="{{ $pts->id }}">

                            <div class="form-group">
                                <label>Nama Perguruan Tinggi</label>
                                <input type="text" class="form-control" value="{{ $pts->nama_pt }}" disabled>
                            </div>

                            <div class="form-group">
                                <label for="tanggal_kegiatan">Tanggal Kegiatan <span
                                        class="text-danger">*</span></label>
                                <input type="text" id="tanggal_kegiatan"
                                    class="form-control modern-date-picker @error('tanggal_kegiatan') is-invalid @enderror"
                                    name="tanggal_kegiatan" value="{{ old('tanggal_kegiatan') }}" required>
                                @error('tanggal_kegiatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Include Flatpickr CSS -->
                            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

                            <!-- Include Flatpickr JS -->
                            <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

                            <script>
                                document.addEventListener("DOMContentLoaded", function () {
                                    flatpickr("#tanggal_kegiatan", {
                                        dateFormat: "Y-m-d",
                                        defaultDate: "{{ old('tanggal_kegiatan') ? old('tanggal_kegiatan') : 'today' }}",
                                        altInput: true,
                                        altFormat: "F j, Y",
                                        locale: "id" // Optional for Indonesian localization
                                    });
                                });
                            </script>
                            <div class="form-group">
                                <label>Tempat Kegiatan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('tempat_kegiatan') is-invalid @enderror"
                                    name="tempat_kegiatan" value="{{ old('tempat_kegiatan') }}" required>
                                @error('tempat_kegiatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Jenis Kegiatan <span class="text-danger">*</span></label>
                                <select class="form-control modern-select @error('jenis_kegiatan') is-invalid @enderror"
                                    name="jenis_kegiatan" required>
                                    <option value="">Pilih Jenis Kegiatan</option>
                                    <option value="Rapat/Audiensi" {{ old('jenis_kegiatan')=='Rapat/Audiensi'
                                        ? 'selected' : '' }}>
                                        Rapat/Audiensi
                                    </option>
                                    <option value="Visitasi" {{ old('jenis_kegiatan')=='Visitasi' ? 'selected' : '' }}>
                                        Visitasi
                                    </option>
                                    <option value="Monitoring & Evaluasi" {{
                                        old('jenis_kegiatan')=='Monitoring & Evaluasi' ? 'selected' : '' }}>
                                        Monitoring & Evaluasi
                                    </option>
                                    <option value="Aduan/Laporan" {{ old('jenis_kegiatan')=='Aduan/Laporan' ? 'selected'
                                        : '' }}>
                                        Aduan/Laporan
                                    </option>
                                    <option value="Teguran/Sanksi" {{ old('jenis_kegiatan')=='Teguran/Sanksi'
                                        ? 'selected' : '' }}>
                                        Teguran/Sanksi
                                    </option>
                                </select>
                                @error('jenis_kegiatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Dokumen Notula (PDF) <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file"
                                        class="custom-file-input @error('dokumen_notula') is-invalid @enderror"
                                        id="dokumen_notula" name="dokumen_notula" accept=".pdf" required>
                                    <label class="custom-file-label" for="dokumen_notula">Pilih file</label>
                                    @error('dokumen_notula')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">Format: PDF, Maksimal: 2MB</small>
                            </div>

                            <div class="form-group">
                                <label>Dokumen Undangan (PDF) <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file"
                                        class="custom-file-input @error('dokumen_undangan') is-invalid @enderror"
                                        id="dokumen_undangan" name="dokumen_undangan" accept=".pdf" required>
                                    <label class="custom-file-label" for="dokumen_undangan">Pilih file</label>
                                    @error('dokumen_undangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">Format: PDF, Maksimal: 2MB</small>
                            </div>

                            <div class="form-group">
                                <label>Ringkasan <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('resume') is-invalid @enderror" name="resume"
                                    maxlength="500" rows="4"
                                    oninput="document.getElementById('charCount').textContent = this.value.length + '/500'"
                                    required>{{ old('resume') }}</textarea>
                                <small class="form-text text-muted">Karakter: <span id="charCount">0/500</span></small>
                                @error('resume')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Nama Pembuat <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('createdbyuser') is-invalid @enderror"
                                    name="createdbyuser" value="{{ old('createdbyuser') }}" required>
                                @error('createdbyuser')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                                <a href="{{ route('laporan-pts.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Update label file input ketika file dipilih
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    // Set initial char count for resume
    document.addEventListener('DOMContentLoaded', function() {
        const textarea = document.querySelector('textarea[name="resume"]');
        document.getElementById('charCount').textContent = textarea.value.length + '/500';
    });
</script>
@endpush

@push('styles')
<style>
    .text-danger {
        color: #dc3545;
    }
</style>
@endpush