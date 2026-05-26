@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Biaya Harian</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Keuangan</li>
        </ol>

        @include('admin.partials.flash')

        @include('admin.partials.date-navigator', [
            'navBaseUrl'  => url('/admin/biaya'),
            'navTanggal'  => $tanggal,
            'navTanggalStr' => $tanggalStr,
        ])

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Data Biaya — {{ $tanggalStr }}</span>
                <a class="btn btn-primary btn-sm" href="{{ url('/admin/biaya/create?tanggal='.$tanggalStr) }}">
                    <i class="fa-solid fa-plus me-1"></i>Tambah
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Item</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($biaya as $row)
                                <tr>
                                    <td class="text-nowrap">{{ $row->tanggal->format('Y-m-d') }}</td>
                                    <td>{{ $row->detail_count }}</td>
                                    <td class="text-end fw-semibold">{{ number_format((float) $row->total_biaya, 2) }}</td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ url('/admin/biaya/'.$row->id.'/edit') }}">Edit</a>
                                        <form class="d-inline" method="post" action="{{ url('/admin/biaya/'.$row->id) }}" onsubmit="return confirm('Hapus biaya harian ini?')">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
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

                {{ $biaya->links() }}
            </div>
        </div>
    </div>
@endsection

