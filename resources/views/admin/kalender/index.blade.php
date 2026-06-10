@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Kalender Operasional</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Master Data</li>
        </ol>

        @include('admin.partials.flash')

        <div class="card mb-4">
            <div class="card-header">
                <form class="row g-2 align-items-end" method="get" action="{{ url('/admin/kalender') }}">
                    <div class="col-6 col-md-2">
                        <label class="form-label mb-0">Bulan</label>
                        <input type="number" min="1" max="12" name="bulan" value="{{ $bulan }}" class="form-control">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label mb-0">Tahun</label>
                        <input type="number" min="2000" max="2100" name="tahun" value="{{ $tahun }}" class="form-control">
                    </div>
                    <div class="col-12 col-md-8 d-flex gap-2">
                        <button class="btn btn-outline-primary" type="submit">Tampilkan</button>
                        <a class="btn btn-outline-secondary" href="{{ url('/admin/kalender') }}">Reset</a>
                        <a class="btn btn-primary ms-auto" href="{{ url('/admin/kalender/create') }}">Tambah</a>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column flex-lg-row gap-3 justify-content-between align-items-lg-center mb-3">
                    <div>
                        <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                            <span class="fw-semibold">Status bulan {{ str_pad($bulan, 2, '0', STR_PAD_LEFT) }}/{{ $tahun }}</span>
                            @if ($ringkasanBulan['statusBulan'] === 'kosong')
                                <span class="badge text-bg-danger">Belum ada tanggal</span>
                            @elseif ($ringkasanBulan['statusBulan'] === 'belum_lengkap')
                                <span class="badge text-bg-warning">Belum lengkap</span>
                            @else
                                <span class="badge text-bg-success">Lengkap</span>
                            @endif
                        </div>
                        <div class="small text-body-secondary">
                            Terisi {{ $ringkasanBulan['totalTerisi'] }} dari {{ $ringkasanBulan['totalHari'] }} hari.
                            Operasional: {{ $ringkasanBulan['totalOperasional'] }},
                            libur: {{ $ringkasanBulan['totalLibur'] }},
                            kurang: {{ $ringkasanBulan['totalKurang'] }}.
                        </div>
                    </div>
                    <form method="post" action="{{ route('kalender.generate-bulan') }}"
                          onsubmit="return confirm('Generate kalender untuk bulan ini? Tanggal yang sudah ada tidak akan diubah.')">
                        @csrf
                        <input type="hidden" name="bulan" value="{{ $bulan }}">
                        <input type="hidden" name="tahun" value="{{ $tahun }}">
                        <button class="btn btn-success" type="submit">
                            Generate Bulan Ini
                        </button>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kalender as $row)
                                <tr>
                                    <td class="text-nowrap">{{ $row->tanggal->format('Y-m-d') }}</td>
                                    <td>
                                        @if ($row->status === 'libur')
                                            <span class="badge text-bg-danger">Libur</span>
                                        @else
                                            <span class="badge text-bg-success">Operasional</span>
                                        @endif
                                    </td>
                                    <td class="text-truncate" style="max-width:180px;">{{ $row->keterangan }}</td>
                                    <td class="text-end text-nowrap">
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-inline-edit"
                                            data-target="edit-row-{{ $row->id }}">Edit</button>
                                        <form class="d-inline" method="post" action="{{ url('/admin/kalender/'.$row->id) }}" onsubmit="return confirm('Hapus tanggal ini?')">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                <tr id="edit-row-{{ $row->id }}" class="d-none table-warning">
                                    <td colspan="4" class="py-2 px-3">
                                        <form method="post" action="{{ url('/admin/kalender/'.$row->id) }}"
                                              class="row g-2 align-items-end">
                                            @csrf
                                            @method('put')
                                            <input type="hidden" name="_bulan" value="{{ $bulan }}">
                                            <input type="hidden" name="_tahun" value="{{ $tahun }}">
                                            <div class="col-12 col-sm-3">
                                                <label class="form-label form-label-sm mb-1">Tanggal</label>
                                                <input type="date" name="tanggal"
                                                    value="{{ $row->tanggal->format('Y-m-d') }}"
                                                    class="form-control form-control-sm" required>
                                            </div>
                                            <div class="col-12 col-sm-3">
                                                <label class="form-label form-label-sm mb-1">Status</label>
                                                <select class="form-select form-select-sm" name="status" required>
                                                    <option value="operasional" {{ $row->status === 'operasional' ? 'selected' : '' }}>Operasional</option>
                                                    <option value="libur" {{ $row->status === 'libur' ? 'selected' : '' }}>Libur</option>
                                                </select>
                                            </div>
                                            <div class="col-12 col-sm-4">
                                                <label class="form-label form-label-sm mb-1">Keterangan</label>
                                                <input type="text" name="keterangan"
                                                    value="{{ $row->keterangan }}"
                                                    class="form-control form-control-sm">
                                            </div>
                                            <div class="col-12 col-sm-2 d-flex gap-1">
                                                <button class="btn btn-sm btn-primary flex-fill" type="submit">Simpan</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary btn-cancel-edit"
                                                    data-target="edit-row-{{ $row->id }}">✕</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-body-secondary">Belum ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.btn-inline-edit').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var row = document.getElementById(btn.dataset.target);
        var isOpen = !row.classList.contains('d-none');
        // tutup semua edit row lain
        document.querySelectorAll('tr[id^="edit-row-"]').forEach(function (r) { r.classList.add('d-none'); });
        if (!isOpen) row.classList.remove('d-none');
    });
});
document.querySelectorAll('.btn-cancel-edit').forEach(function (btn) {
    btn.addEventListener('click', function () {
        document.getElementById(btn.dataset.target).classList.add('d-none');
    });
});
</script>
@endpush
