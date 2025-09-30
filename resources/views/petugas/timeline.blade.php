<div class="profiletimeline">
    @foreach ($data as $item)
        <div class="sl-item">
                <div class="sl-left">
                    @if ($item->kunjungan_foto != NULL)
                            @if (Storage::disk('public')->exists($item->kunjungan_foto))
                            <img src="{{asset('storage'.$item->kunjungan_foto)}}" alt="user" class="img-circle" />
                            @else
                                <img src="https://placehold.co/480x480/0022FF/FFFFFF/?text=photo+tidak+ada" class="img-circle" alt="user" />
                            @endif
                    @else
                        <img src="https://placehold.co/480x480/0022FF/FFFFFF/?text=photo+tidak+ada" class="img-circle" alt="user" />
                    @endif
                </div>
                <div class="sl-right">
                 <div><a href="javascript:void(0)" class="link" data-uid="{{$item->kunjungan_uid}}" data-toggle="modal" data-target="#ViewKunjunganModal">{{$item->Pengunjung->pengunjung_nama}}</a> <span class="sl-date">{{$item->kunjungan_tanggal}}</span>
                    <blockquote class="m-t-10">
                        <p><strong>Keperluan</strong> : {{$item->kunjungan_keperluan}}</p>
                        <p><strong>Tindak lanjut</strong> : {{$item->kunjungan_tindak_lanjut}}</p>
                        @if ($item->kunjungan_tujuan == 1)
                            <span class="label label-danger">{{$item->Tujuan->tujuan_nama}}</span> <span class="label label-info">{{$item->LayananKantor->layanan_kantor_nama}}</span>
                        @elseif($item->kunjungan_tujuan == 2)
                            <span class="label label-danger">{{$item->Tujuan->tujuan_nama}}</span> <span class="label label-info">{{$item->LayananPst->layanan_pst_nama}}</span>
                        @else
                            <span class="label label-danger">{{$item->Tujuan->tujuan_nama}}</span>
                        @endif
                        @if ($item->kunjungan_flag_feedback == 'sudah')
                            @for ($i = 1; $i < 7; $i++)
                                @if ($i <= $item->kunjungan_nilai_feedback)
                                    <span class="fa fa-star text-warning"></span>
                                @else
                                    <span class="fa fa-star"></span>
                                @endif
                            @endfor
                        @endif
                    </blockquote>
                </div>
                </div>
        </div>
    @endforeach
</div>
