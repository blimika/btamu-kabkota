@component('mail::message')

# Hai {{ $body->nama_lengkap }}
Terimakasih, telah berkunjung {{$body->nama_satker}}.
Berikut nomor antrian Anda!


# Detil Kunjungan <br>
Nama : {{ $body->nama_lengkap }} <br>
Email : {{ $body->email }} <br>
Telepon : {{ $body->telepon }} <br>
Tanggal Kunjungan : {{ $body->tanggal }} <br>


@component('mail::panel')
Layanan : {{ $body->layanan_utama }} <br>
# Nomor Antrian : {{ $body->nomor_antrian }} <br />
@endcomponent


Terimakasih,<br>
{{$body->nama_aplikasi}} <br>
{{$body->nama_satker}}<br>
{{$body->alamat_satker}}

@endcomponent
