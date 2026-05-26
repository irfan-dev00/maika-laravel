{{--
    Date Navigator Partial
    Required: $navBaseUrl (string), $navTanggal (Carbon), $navTanggalStr (string)
    Optional: $navExtraParams (array key=>val — extra query params dilestarikan di prev/next/today & hidden inputs)
--}}
@php
    $navPrev     = $navTanggal->copy()->subDay()->toDateString();
    $navNext     = $navTanggal->copy()->addDay()->toDateString();
    $navToday    = \Carbon\Carbon::today()->toDateString();
    $navIsToday  = $navTanggalStr === $navToday;

    $namaHari  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    $namaBulan = ['Januari','Februari','Maret','April','Mei','Juni',
                  'Juli','Agustus','September','Oktober','November','Desember'];
    $navHariNama  = $namaHari[$navTanggal->dayOfWeek];
    $navBulanNama = $namaBulan[$navTanggal->month - 1];

    $navExtra  = $navExtraParams ?? [];
    $navQs     = '';
    foreach ($navExtra as $k => $v) {
        if ($v !== null && $v !== '') $navQs .= '&' . urlencode($k) . '=' . urlencode($v);
    }
@endphp
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            {{-- Prev --}}
            <a href="{{ $navBaseUrl }}?tanggal={{ $navPrev }}{{ $navQs }}"
               class="btn btn-outline-secondary btn-sm" title="{{ $navPrev }}">
                <i class="fa-solid fa-chevron-left"></i>
            </a>

            {{-- Date picker form --}}
            <form method="get" action="{{ $navBaseUrl }}" class="d-flex align-items-center gap-2">
                @foreach ($navExtra as $k => $v)
                    @if ($v !== null && $v !== '')
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endif
                @endforeach
                <div class="text-center" style="min-width:160px">
                    <div class="fw-bold fs-5 lh-1">
                        {{ $navHariNama }}, {{ $navTanggal->day }} {{ $navBulanNama }}
                    </div>
                    <div class="text-body-secondary small">{{ $navTanggal->year }}</div>
                </div>
                <input type="date" name="tanggal" value="{{ $navTanggalStr }}"
                       class="form-control form-control-sm" style="width:150px"
                       onchange="this.form.submit()">
            </form>

            {{-- Next --}}
            <a href="{{ $navBaseUrl }}?tanggal={{ $navNext }}{{ $navQs }}"
               class="btn btn-outline-secondary btn-sm" title="{{ $navNext }}">
                <i class="fa-solid fa-chevron-right"></i>
            </a>

            {{-- Hari Ini (hanya tampil kalau bukan hari ini) --}}
            @if (! $navIsToday)
                <a href="{{ $navBaseUrl }}?tanggal={{ $navToday }}{{ $navQs }}"
                   class="btn btn-outline-primary btn-sm">
                    <i class="fa-solid fa-calendar-day me-1"></i>Hari Ini
                </a>
            @endif
        </div>
    </div>
</div>
