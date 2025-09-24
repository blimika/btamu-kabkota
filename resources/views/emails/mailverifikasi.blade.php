@component('mail::message')
# Hai {{$body->nama_lengkap}}

Permintaan penggantian alamat email baru. silakan klik link aktivasi dibawah ini.

@component('mail::button', ['url' => $body->link_aktivasi])
AKTIVASI EMAIL
@endcomponent

@component('mail::panel')
Jika mengalami kendala dalam klik tombol aktivasi, silakan copy paste link dibawah ini <br>
<strong>{{$body->link_aktivasi}}</strong>
@endcomponent

Terimakasih,<br>
{{$body->nama_aplikasi}} <br>
{{$body->nama_satker}}<br>
{{$body->alamat_satker}}
@endcomponent
