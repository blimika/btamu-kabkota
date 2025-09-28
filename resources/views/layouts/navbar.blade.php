<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li class="user-pro">
                    <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
                        <!---<img src="../assets/images/users/1.jpg" alt="user-img" class="img-circle">--->
                        <span class="hide-menu">
                        @if (Auth::user())
                            {{Auth::user()->name}}
                        @else
                            MASUK
                        @endif
                        </span></a>
                    <ul aria-expanded="false" class="collapse">
                        @if (Auth::user())
                            <li><a href="javascript:void(0)"><i class="ti-user"></i> My Profile</a></li>
                            <li><a href="{{route('logout')}}"><i class="fa fa-power-off"></i> Logout</a></li>
                        @else
                            <li><a href="{{route('login')}}"><i class="fa fa-power-off"></i> Login</a></li>
                        @endif
                    </ul>
                </li>
                <li class="nav-small-cap">--- DEPAN</li>
                <li> <a class="waves-effect waves-dark" href="{{url('')}}"><i class="icon-speedometer"></i><span class="hide-menu">Dashboard</span></a>
                </li>
                @if (Auth::user() || Generate::CekAkses(\Request::getClientIp(true)))
                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="ti-layout-grid2"></i><span class="hide-menu">Tambah Data</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{route('kunjungan.tambah')}}">Kunjungan</a></li>
                        <li><a href="{{route('permintaan.tambah')}}">Permintaan</a></li>
                    </ul>
                </li>
                <li> <a class="waves-effect waves-dark" href="{{route('display.antrian')}}" target="_blank"><i class="ti-email"></i><span class="hide-menu">Display Antrian</span></a>
                </li>
                @if (Auth::user())
                <li class="nav-small-cap">--- KUNJUNGAN</li>
                <li> <a class="waves-effect waves-dark" href="{{route('kunjungan.index')}}" aria-expanded="false"><i class="icon-people"></i><span class="hide-menu">List Kunjungan</span></a>
                </li>
                <li> <a class="waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="icon-chart"></i><span class="hide-menu">Laporan</span></a>
                </li>
                <li class="nav-small-cap">--- PENGUNJUNG</li>
                <li> <a class="waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="fa fa-graduation-cap"></i><span class="hide-menu">List Pengunjung</span></a>
                </li>
                <li> <a class="waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="ti-files"></i><span class="hide-menu">Feedback</span></a>
                </li>
                <li class="nav-small-cap">--- MASTER</li>
                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="ti-gallery"></i><span class="hide-menu">Petugas</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{route('petugas.index')}}">List Petugas</a></li>
                        <li><a href="{{route('petugas.nilai')}}">Penilaian</a></li>
                    </ul>
                </li>
                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="ti-files"></i><span class="hide-menu">Jadwal </span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{route('master.tanggal')}}">List Jadwal</a></li>
                        <li><a href="{{route('master.kalendar')}}">Kalendar</a></li>
                    </ul>
                </li>
                @if (Auth::user()->user_level == 'admin')
                    <li> <a class="waves-effect waves-dark" href="{{route('master.tujuan')}}" aria-expanded="false"><i class="ti-location-pin"></i><span class="hide-menu">Tujuan</span></a>
                    </li>
                    <li> <a class="waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="ti-location-pin"></i><span class="hide-menu">Daftar Akses</span></a>
                    </li>
                    <li> <a class="waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="fab fa-whatsapp"></i><span class="hide-menu">Whatsapp</span></a>
                    </li>
                @endif
                @endif
                @endif
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
