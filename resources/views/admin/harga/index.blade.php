@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Harga Bulanan</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Master Data</li>
        </ol>

        @include('admin.partials.flash')

        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex flex-wrap justify-content-between gap-2">
                    <form class="row g-2 align-items-end" method="get" action="{{ url('/admin/harga') }}">
                        <div class="col-12 col-md-4">
                            <label class="form-label mb-0">Mitra</label>
                            <select class="form-select" name="mitra_id">
                                <option value="">Semua</option>
                                @foreach ($mitra as $m)
                                    <option value="{{ $m->id }}" {{ (string) $mitraId === (string) $m->id ? 'selected' : '' }}>{{ $m->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label mb-0">Tahun</label>
                            <input type="number" class="form-control" name="tahun" value="{{ $tahun }}" placeholder="2026">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label mb-0">Bulan</label>
                            <input type="number" class="form-control" name="bulan" value="{{ $bulan }}" min="1" max="12" placeholder="1-12">
                        </div>
                        <div class="col-12 col-md-4 d-flex gap-2">
                            <button class="btn btn-outline-primary" type="submit">Filter</button>
                            <a class="btn btn-outline-secondary" href="{{ url('/admin/harga') }}">Reset</a>
                            <a class="btn btn-primary ms-auto" href="{{ url('/admin/harga/create') }}">Tambah</a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Periode</th>
                                <th>Mitra</th>
                                <th>Produk</th>
                                <th class="text-end">Harga Jual</th>
                                <th class="text-end">Margin/Unit</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($harga as $row)
                                <tr>
                                    <td class="text-nowrap">{{ $row->tahun }}-{{ str_pad((string) $row->bulan, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ optional($row->mitra)->nama }}</td>
                                    <td>{{ optional($row->produk)->nama }}</td>
                                    <td class="text-end">{{ number_format((float) $row->harga_jual, 2) }}</td>
                                    <td class="text-end">{{ $row->margin_per_unit !== null ? number_format((float) $row->margin_per_unit, 2) : '-' }}</td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ url('/admin/harga/'.$row->id.'/edit') }}">Edit</a>
                                        <form class="d-inline" method="post" action="{{ url('/admin/harga/'.$row->id) }}" onsubmit="return confirm('Hapus data harga ini?')">
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

                {{ $harga->links() }}
            </div>
        </div>
    </div>
@endsection

