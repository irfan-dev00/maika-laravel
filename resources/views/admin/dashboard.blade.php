@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Dashboard</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Ringkasan</li>
        </ol>

        <div class="row g-3">
            <div class="col-6 col-lg-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">Mitra Aktif</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <span class="small text-white">0</span>
                        <div class="small text-white"><i class="fa-solid fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card bg-success text-white">
                    <div class="card-body">Produk</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <span class="small text-white">0</span>
                        <div class="small text-white"><i class="fa-solid fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">Omzet Hari Ini</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <span class="small text-white">0</span>
                        <div class="small text-white"><i class="fa-solid fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">Pembayaran Hari Ini</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <span class="small text-white">0</span>
                        <div class="small text-white"><i class="fa-solid fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">Langkah awal</div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-outline-secondary" href="{{ url('/admin/mitra') }}">Tambah Mitra</a>
                    <a class="btn btn-outline-secondary" href="{{ url('/admin/produk') }}">Tambah Produk</a>
                    <a class="btn btn-outline-secondary" href="{{ url('/admin/harga') }}">Set Harga Bulanan</a>
                    <a class="btn btn-outline-secondary" href="{{ url('/admin/kalender') }}">Set Kalender</a>
                </div>
            </div>
        </div>
    </div>
@endsection
