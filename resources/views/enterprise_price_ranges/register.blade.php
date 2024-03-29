@extends('layouts.user_type.auth')

@section('content')
    <section class="min-vh-100 mb-8">
        <div class="container" style="margin-top: 200px;">
            <div class="row mt-lg-n10 mt-md-n11 mt-n10">
                <div class="col-xl-4 col-lg-5 col-md-7 mx-auto">
                    <div class="card z-index-0">
                        <div class="card-header text-center pt-4">
                            <img src="{{ asset('assets\img\icone-atrelamento.png') }}" width="90%" height="90%">
                            <h5>Cadastro de nova faixa nas empresas</h5>
                        </div>
                        <div class="card-body">
                            <form role="form text-left" method="POST" action="/enterprise-price-range-management-new"
                                id="pricebandForm">
                                @csrf
                                <div class="mb-3">
                                    <select class="form-control" id="enterprise_id" name="enterprise_id"
                                        aria-label="enterprise_id" aria-describedby="enterprise_id">
                                        <option value="">Selecione uma empresa</option>
                                        @foreach ($enterprises as $enterprise)
                                            <option value="{{ $enterprise->id }}">{{ $enterprise->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('enterprise_id')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <select class="form-control" id="price_band_id" name="price_band_id"
                                        aria-label="price_band_id" aria-describedby="price_band_id">
                                        <option value="">Selecione uma faixa de preço</option>
                                        @foreach ($priceBands as $priceBand)
                                            <option value="{{ $priceBand->id }}">
                                                {{ 'R$' . number_format($priceBand->value, 2, ',', '.') }}</option>
                                        @endforeach
                                    </select>
                                    @error('price_band_id')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <select class="form-control" id="city_id" name="city_id" aria-label="city_id"
                                        aria-describedby="city_id">
                                        <option value="">Selecione uma Cidade</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('city_id')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn bg-gradient-dark w-100 my-4 mb-2">Cadastrar</button>
                                    <a href="/enterprise-price-range-management"
                                        class="btn bg-gradient-dark w-100 my-4 mb-2">Voltar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resultModalLabel">Mensagem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success d-none" role="alert" id="successMessage"></div>
                    <div class="alert alert-danger d-none text-white" role="alert" id="errorMessage"></div>
                </div>
                <div class="modal-footer">
                    <a href="/enterprise-price-range-management" type="button" class="btn btn-secondary">Voltar para tela
                        anterior</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('pricebandForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Impede o envio do formulário

            // Realize a chamada AJAX para enviar o formulário
            var formData = new FormData(this);

            fetch(this.action, {
                    method: this.method,
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('successMessage').textContent = data.success;
                        document.getElementById('successMessage').classList.remove('d-none');
                        document.getElementById('errorMessage').classList.add('d-none');
                    } else if (data.error) {
                        document.getElementById('errorMessage').textContent = data.error;
                        document.getElementById('errorMessage').classList.remove('d-none');
                        document.getElementById('successMessage').classList.add('d-none');
                    }

                    var resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
                    resultModal.show();
                })
                .catch(error => {
                    console.error('Ocorreu um erro:', error);
                });
        });
    </script>
@endsection
