@extends('layouts.user_type.auth')

@section('content')
    <div>
        <div class="container-fluid py-4">
            <div class="card">
                <div class="card-header pb-0 px-3">
                    <h6 class="mb-0">{{ __('Dados do Lançamento') }}</h6>
                </div>
                <div class="card-body pt-4 p-3">
                    <form action="/posting-management-edit/{{ $priceBandEdit->id }}" method="POST" role="form text-left">
                        @csrf
                        @if ($errors->any())
                            <div class="mt-3  alert alert-primary alert-dismissible fade show" role="alert">
                                <span class="alert-text text-white">
                                    {{ $errors->first() }}</span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                    <i class="fa fa-close" aria-hidden="true"></i>
                                </button>
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="m-3  alert alert-success alert-dismissible fade show" id="alert-success"
                                role="alert">
                                <span class="alert-text text-white">
                                    {{ session('success') }}</span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                    <i class="fa fa-close" aria-hidden="true"></i>
                                </button>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="posting-value" class="form-control-label">{{ __('Valor') }}</label>
                                    <div class="@error('posting.value')border border-danger rounded-3 @enderror">
                                        <input class="form-control" value="{{ $priceBandEdit->value }}" type="text"
                                            placeholder="Valor" id="posting-value" name="value">
                                        @error('value')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="/posting-management" class="btn bg-gradient-dark btn-md mt-4 mb-4">Voltar</a>
                            <span class="mx-2"></span>
                            <button type="submit"
                                class="btn bg-gradient-dark btn-md mt-4 mb-4">{{ 'Salvar alterações' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
