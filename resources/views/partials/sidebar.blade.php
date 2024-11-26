<div class="main-sidebar sidebar-style-2 animated-sidebar" style="background-image: url('{{ asset('image/bgblue.jpg') }}'); background-size: cover; background-position: center;">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand text-center">
            <a href="{{route ('home')}}">
                <div class="logo-container-large">
                    <img src="{{ asset('logo-jaki.png') }}" alt="SI-JAKI Logo" class="brand-image-large">
                </div>
            </a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm text-center">
            <a href="{{route ('home')}}">
                <div class="logo-container-small">
                    <img src="{{ asset('logo/logo-sijaki-sidebar.png') }}" alt="SI-JAKI Logo" class="brand-image-small">
                </div>
            </a>
        </div>
        <ul class="sidebar-menu">
            @if (Auth::user()->akses === 'Admin')
            <li class="menu-header">Manajemen Data</li>
            <li class="dropdown {{ Request::is('pts*') || Request::is('ptn*') || Request::is('users*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-database" style="color: #1F509A"></i><span>Master Data</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('pts*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('pts.index') }}">Perguruan Tinggi Swasta</a>
                    </li>
                    <li class="{{ Request::is('ptn*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('ptn.index') }}">Perguruan Tinggi Negeri</a>
                    </li>
                    <li class="{{ Request::is('users*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('user.index') }}">Users</a>
                    </li>
                </ul>
            </li>
            @endif

            <li class="menu-header">Manajemen Laporan</li>
            <li class="dropdown {{ Request::is('laporan-pts*') || Request::is('laporan-ptn*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-folder" style="color: #1F509A"></i> <span>Dokumen</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('laporan-pts*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('laporan-pts.index') }}">Laporan PTS</a>
                    </li>
                    <li class="{{ Request::is('laporan-ptn*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('laporan-ptn.index') }}">Laporan PTN</a>
                    </li>
                </ul>
            </li>
        </ul>

        <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
            <a href="{{ route('home') }}" class="btn btn-primary btn-lg btn-block btn-icon-split">
                <i class="fas fa-home"></i> Beranda
            </a>
        </div>
    </aside>
</div>

<style>
    .main-sidebar {
        color: #0077f6; /* Warna teks agar kontras dengan background */
    }

    .logo-container-large, .logo-container-small {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 80px;
        overflow: hidden;
        margin-bottom: 15px; /* Menambahkan jarak antara gambar dan teks berikutnya */
    }

    .brand-image-large  {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .brand-image-small {
        max-width: 80%;
        max-height: 80%;
        object-fit: contain;
    }

    .sidebar-menu .menu-header {
        margin-top: 20px;
        color: #0077f6; /* Ubah warna teks header menu */
    }

    .sidebar-menu .nav-link {
        color: #0077f6; /* Warna link menu */
    }

    /* Animasi untuk background */
    .animated-sidebar {
        animation: background-move 10s linear infinite; /* Animasi berlangsung selama 10 detik */
    }

    @keyframes background-move {
        0% {
            background-position: 0% center;
        }
        50% {
            background-position: 100% center;
        }
        100% {
            background-position: 0% center;
        }
    }
</style>
