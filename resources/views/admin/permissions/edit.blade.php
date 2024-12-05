@extends('admin.layout')
@section('content')
    <div class="page-wrapper">
        <div class="page-content">

            <div class="row">
                <div class="col-xl-10 mx-auto">

                    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                        <div class="breadcrumb-title pe-3">Permission</div>
                        <div class="ps-3">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 p-0">
                                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">Edit Permission</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="ms-auto">
                            <a href="{{ route('permissions.index') }}" class="btn btn-primary px-5">
                                Permission list
                            </a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body p-4">

                            <h5 class="mb-4">Edit Permission</h5>

                            <form action="{{ route('permissions.update', $permission->id) }}" method="POST"
                                class="row g-3">
                                @csrf
                                @method('PUT')

                                <div class="col-md-12">
                                    <label for="name" class="form-label">Permission Name</label>
                                    <input type="text" name="name" class="form-control" id="name"
                                        placeholder="Permission Name" value="{{ old('name', $permission->name) }}">
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <div class="d-md-flex d-grid align-items-center gap-3">
                                        <button type="submit" class="btn btn-primary px-4">Update</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
