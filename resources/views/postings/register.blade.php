@extends('layouts.user_type.auth')

@section('content')
    <section class="min-vh-100 mb-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <div class="container" style="margin-top: 200px;">
            <div class="row mt-lg-n10 mt-md-n11 mt-n10">
                <div class="col-xl-10 col-lg-10 col-md-12 mx-auto">
                    <div class="card z-index-0">
                        <div class="card-header text-center pt-4">
                            <img src="{{ asset('assets\img\icone-lancamento.png') }}" width="30%" height="30%">
                            <h5>Cadastro de novo Lançamento</h5>
                        </div>
                        <div class="card-body">
                            <form role="form text-left" id="postingForm">
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
                                            <input type="text" class="form-control" id="fixedValue" name="fixedValue"
                                                placeholder="Valor Fixo" style="display: none;">
                                            <select class="form-control" id="enterprisePriceRange"
                                                name="enterprisePriceRange" aria-label="enterprisePriceRange"
                                                aria-describedby="enterprisePriceRange" required>
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
                                                <option value="entrega">Entrega</option>
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
                                            <input type="text" class="form-control" id="date" name="date"
                                                readonly>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="isNote" name="isNote">
                                            <label class="form-check-label" for="isNote">
                                                Marcar como nota
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <select class="form-control" id="enterprise" name="enterprise"
                                            style="display: none;
                                                aria-label="enterprise"
                                            aria-describedby="enterprise">
                                            <option value="">Empresa</option>
                                            @foreach ($enterprises as $enterprise)
                                                <option value="{{ $enterprise->name }}">
                                                    {{ $enterprise->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <input type="hidden" name="user" id="user" value="{{ $user->id }}">
                                </div>
                                <div class="text-center">
                                    <div class="btn-group">
                                        <button type="button" onclick="preventDuplicated()"
                                            class="btn btn-dark btn-lg mx-1" id="submit">Cadastrar</button>
                                        <a href="/posting-management"
                                            class="btn btn-dark btn-lg mx-1 d-none d-sm-block">Voltar</a>
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

    <div class="modal fade" id="resultModalDuplicated" tabindex="-1" aria-labelledby="resultModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resultModalLabelDuplicated">Mensagem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning d-none" role="alert" id="duplicatedMessage"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="createNewPosting()">Registrar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(document).ready(function() {
            $('#type').change(function() {
                if ($(this).val() == 'entrega') {
                    $('#enterprisePriceRange').hide();
                    $('#fixedValue').show();
                    $('#enterprise').show();

                } else {
                    $('#enterprisePriceRange').show();
                    $('#fixedValue').hide();
                    $('#enterprise').hide();

                }
            });
        });
        $(function() {
            $("#date").datepicker({
                dateFormat: "dd/mm/yy"
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            var currentDate = new Date();
            var year = currentDate.getFullYear();
            var month = ("0" + (currentDate.getMonth() + 1)).slice(-2);
            var day = ("0" + currentDate.getDate()).slice(-2);

            var formattedDate = year + "-" + month + "-" + day;
            document.getElementById("date").value = formattedDate;
        });

        function preventDuplicated() {
            $("#submit").prop("disabled", true);
            let formDataObject = {};
            const deliverer = document.getElementById('deliverer').value;
            const date = document.getElementById('date').value;
            const enterprisePriceRange = document.getElementById('enterprisePriceRange').value;
            const quantity = document.getElementById('quantity').value;
            const type = document.getElementById('type').value;
            const user = document.getElementById('user').value;
            const isNote = document.getElementById('isNote').checked;
            const fixedValue = document.getElementById('fixedValue').value;
            const enterprise = document.getElementById('enterprise').value;

            if (type == 'entrega') {
                formDataObject = {
                    deliverer,
                    date,
                    enterprisePriceRange,
                    quantity,
                    type,
                    user,
                    isNote,
                    fixedValue,
                    enterprise
                };
            } else {
                formDataObject = {
                    deliverer,
                    date,
                    enterprisePriceRange,
                    quantity,
                    type,
                    user,
                    isNote
                };
            }

            const formData = new FormData();

            for (const key in formDataObject) {
                formData.append(key, formDataObject[key]);
            }

            fetch('{{ route('preventduplicated') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.duplicated) {
                        document.getElementById('duplicatedMessage').textContent =
                            'Existe um lançamento similar a esse, tem certeza que quer inserir?';
                        document.getElementById('duplicatedMessage').classList.remove('d-none');
                        $('#resultModalDuplicated').modal('show');
                        $("#submit").prop("disabled", false);

                    } else {
                        createNewPosting();
                    }
                })
                .catch(error => {
                    console.error(error);
                });
        }

        function createNewPosting() {
            $('#resultModalDuplicated').modal('hide');
            $("#submit").prop("disabled", true);


            let formDataObject = {};
            const deliverer = document.getElementById('deliverer').value;
            const date = document.getElementById('date').value;
            const enterprisePriceRange = document.getElementById('enterprisePriceRange').value;
            const quantity = document.getElementById('quantity').value;
            const type = document.getElementById('type').value;
            const user = document.getElementById('user').value;
            const isNote = document.getElementById('isNote').checked;
            const fixedValue = document.getElementById('fixedValue').value;
            const enterprise = document.getElementById('enterprise').value;

            if (type == 'entrega') {
                formDataObject = {
                    deliverer,
                    date,
                    enterprisePriceRange,
                    quantity,
                    type,
                    user,
                    isNote,
                    fixedValue,
                    enterprise
                };
            } else {
                formDataObject = {
                    deliverer,
                    date,
                    enterprisePriceRange,
                    quantity,
                    type,
                    user,
                    isNote
                };
            }

            const formData = new FormData();


            for (const key in formDataObject) {
                formData.append(key, formDataObject[key]);
            }

            fetch('{{ route('postingManagementNew') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (response.ok) {
                        return response
                            .json();
                    } else {
                        throw new Error(
                            'Erro na solicitação');
                    }
                })
                .then(data => {
                    $("#submit").prop("disabled", false);

                    if (data && data.success) {
                        document.getElementById('successMessage').textContent = data.success;
                        document.getElementById('successMessage').classList.remove('d-none');
                        document.getElementById('errorMessage').classList.add('d-none');
                    } else if (data && data.error) {
                        document.getElementById('errorMessage').textContent = data.error;
                        document.getElementById('errorMessage').classList.remove('d-none');
                        document.getElementById('successMessage').classList.add('d-none');
                    } else {
                        document.getElementById('errorMessage').textContent =
                            'Não foi possível realizar o lançamento verifique os dados inseridos.';
                        document.getElementById('errorMessage').classList.remove('d-none');
                        document.getElementById('successMessage').classList.add('d-none');
                    }

                    var resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
                    resultModal.show();
                })
                .catch(error => {
                    document.getElementById('errorMessage').textContent =
                        'Não foi possível realizar o lançamento verifique os dados inseridos.';
                    document.getElementById('errorMessage').classList.remove('d-none');
                    document.getElementById('successMessage').classList.add('d-none');

                    $("#submit").prop("disabled", false);
                    console.error('Ocorreu um erro:', error);
                });
        }
    </script>
@endsection
