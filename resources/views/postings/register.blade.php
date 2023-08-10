@extends('layouts.user_type.auth')

@section('content')
    <section class="min-vh-100 mb-8">
        <div class="container" style="margin-top: 200px;">
            <div class="row mt-lg-n10 mt-md-n11 mt-n10">
                <div class="col-xl-10 col-lg-10 col-md-12 mx-auto">
                    <div class="card z-index-0">
                        <div class="card-header text-center pt-4">
                            <img src="{{ asset('assets\img\icone-lancamento.png') }}" width="30%" height="30%">
                            <h5>Cadastro de novo Lançamento</h5>
                        </div>
                        <div class="card-body">
                            <form role="form text-left" method="POST" action="/posting-management-new" id="postingForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <select class="form-control" id="deliverer" name="deliverer"
                                                aria-label="deliverer" aria-describedby="deliverer">
                                                <option value="">Entregador</option>
                                                @foreach ($deliverers as $deliverer)
                                                    <option value="{{ $deliverer->id }}">
                                                        {{ $deliverer->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('deliverer')
                                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <select class="form-control" id="enterprisePriceRange"
                                                name="enterprisePriceRange" aria-label="enterprisePriceRange"
                                                aria-describedby="enterprisePriceRange">
                                                <option value="">Faixa de Preço</option>
                                                @foreach ($enterprisePriceRanges as $enterprisePriceRange)
                                                    <option value="{{ $enterprisePriceRange->id }}">
                                                        {{ $enterprisePriceRange->formatted_data }}</option>
                                                @endforeach
                                            </select>
                                            @error('enterprisePriceRange')
                                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <input type="text" class="form-control" placeholder="Quantidade de Pacotes"
                                                id="quantity" name="quantity" aria-label="quantity"
                                                aria-describedby="quantity">
                                            @error('quantity')
                                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <select class="form-control" id="type" name="type">
                                                <option value="carregamento">Carregamento</option>
                                                <option value="insucesso">Insucesso</option>
                                                <option value="coleta">Coleta</option>
                                            </select>
                                            @error('type')
                                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="date" class="form-control" id="date" name="date">
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="isNote" name="isNote">
                                            <label class="form-check-label" for="isNote">
                                                Marcar como nota
                                            </label>
                                        </div>

                                    </div>

                                </div>
                                <div class="mb-3">
                                    <input type="hidden" name="user" value="{{ $user->id }}">
                                </div>
                                <div class="text-center">
                                    <div class="btn-group">
                                        <button type="submit" class="btn btn-dark btn-lg mx-1">Cadastrar</button>
                                        <a href="/posting-management" class="btn btn-dark btn-lg mx-1">Voltar</a>
                                    </div>
                                </div>
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
                    <a href="/posting-management" type="button" class="btn btn-secondary">Voltar para tela anterior</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var currentDate = new Date();
            var year = currentDate.getFullYear();
            var month = ("0" + (currentDate.getMonth() + 1)).slice(-2);
            var day = ("0" + currentDate.getDate()).slice(-2);

            var formattedDate = year + "-" + month + "-" + day;
            document.getElementById("date").value = formattedDate;
            document.getElementById('postingForm').addEventListener('submit', function(event) {
                event.preventDefault(); // Impede o envio do formulário

                // Realize a chamada AJAX para enviar o formulário
                var formData = new FormData(this);
                console.log(formData);
                fetch(this.action, {
                        method: this.method,
                        body: formData
                    })
                    .then(response => {
                        console.log(response);
                    })
                    .then(data => {

                        console.log(data);
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
        });
    </script>
@endsection
