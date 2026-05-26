@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Pembayaran Mitra</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Keuangan</li>
        </ol>

        @include('admin.partials.flash')

        @include('admin.partials.date-navigator', [
            'navBaseUrl'     => url('/admin/pembayaran'),
            'navTanggal'     => $tanggal,
            'navTanggalStr'  => $tanggalStr,
            'navExtraParams' => ['mitra_id' => $mitraId, 'status' => $status],
        ])

        <div class="card mb-4">
            <div class="card-header">
                <form class="row g-2 align-items-end" method="get" action="{{ url('/admin/pembayaran') }}">
                    <input type="hidden" name="tanggal" value="{{ $tanggalStr }}">
                    <div class="col-12 col-md-4">
                        <label class="form-label mb-0">Mitra</label>
                        <select class="form-select" name="mitra_id">
                            <option value="">Semua</option>
                            @foreach ($mitra as $m)
                                <option value="{{ $m->id }}" {{ (string) $mitraId === (string) $m->id ? 'selected' : '' }}>{{ $m->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label mb-0">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Semua</option>
                            <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-5 d-flex gap-2 flex-wrap">
                        <button class="btn btn-outline-primary" type="submit">Filter</button>
                        <a class="btn btn-outline-secondary" href="{{ url('/admin/pembayaran?tanggal='.$tanggalStr) }}">Reset</a>
                        <a class="btn btn-primary ms-auto" href="{{ url('/admin/pembayaran/create') }}">Tambah</a>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Mitra</th>
                                <th>Laporan</th>
                                <th>Status</th>
                                <th class="text-end">Jumlah Bayar</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pembayaran as $row)
                                <tr>
                                    <td class="text-nowrap">{{ $row->tanggal->format('Y-m-d') }}</td>
                                    <td>{{ optional($row->mitra)->nama }}</td>
                                    <td>#{{ $row->laporan_penjualan_mitra_id }}</td>
                                    <td>
                                        @if ($row->status === 'confirmed')
                                            <span class="badge text-bg-success">Confirmed</span>
                                        @else
                                            <span class="badge text-bg-secondary">Draft</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold">{{ number_format((float) $row->jumlah_bayar, 2) }}</td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ url('/admin/pembayaran/'.$row->id.'/edit') }}">Edit</a>
                                        <form class="d-inline" method="post" action="{{ url('/admin/pembayaran/'.$row->id) }}" onsubmit="return confirm('Hapus pembayaran ini?')">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-body-secondary">Belum ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $pembayaran->links() }}
            </div>
        </div>
    </div>
@endsection

