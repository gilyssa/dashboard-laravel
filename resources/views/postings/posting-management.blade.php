@extends('layouts.user_type.auth')

@section('content')
    <div>
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 mx-4">
                    <div class="card-header pb-0">
                        <div class="flex-row">
                            <div>
                                <h5 class="mb-0">Todos os Lançamentos</h5>
                            </div>
                            @if ($userAccess === 'admin')
                                <a href="{{ url('/posting-management-register') }}"
                                    class="btn bg-gradient-primary btn-sm mb-0 d-block d-sm-inline-block text-icon"
                                    type="button">
                                    <span class="icon">
                                        <i class="fas fa-plus text-white"></i>
                                    </span>
                                    <span class="text d-none d-sm-inline" style="width: 50%; height: 50%;">Cadastrar</span>
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="startDate" class="form-label">Data de Início:</label>
                                <input type="text" class="form-control" id="startDate" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="endDate" class="form-label">Data de Fim:</label>
                                <input type="text" class="form-control" id="endDate" readonly>
                            </div>
                            <button class="btn btn-primary" onclick="filterTableByDate()">Filtrar</button>
                        </div>
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0" style="overflow: auto;">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            ID
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Entregador
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Usuário
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Empresa
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Nota
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Pacotes
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Preço
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Tipo
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Data
                                        </th>
                                        @if ($userAccess !== 'admin')
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Ação
                                            </th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($postings as $posting)
                                        <tr id="row{{ $posting['id'] }}"> <!-- Add an ID for each row -->
                                            <td class="ps-4">
                                                <p class="text-xs font-weight-bold mb-0">{{ $posting['id'] }}</p>
                                            </td>
                                            <td class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">{{ $posting['deliverer'] }}</p>
                                            </td>
                                            <td class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">{{ $posting['user'] }}</p>
                                            </td>
                                            <td class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">{{ $posting['priceRange'] }}</p>
                                            </td>
                                            <td class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">
                                                    {{ $posting['isNote'] == 1 ? 'Sim' : 'Não' }}</p>
                                            </td>
                                            <td class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">{{ $posting['quantity'] }}</p>
                                            </td>
                                            <td class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">{{ $posting['currentPrice'] }}</p>
                                            </td>
                                            <td class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">{{ strtoupper($posting['type']) }}
                                                </p>
                                            </td>
                                            <td class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">{{ $posting['date'] }}</p>
                                            </td>
                                            @if ($userAccess === 'admin')
                                                <td class="text-center">
                                                    <a class="mx-3" data-bs-toggle="tooltip"
                                                        data-bs-original-title="Edit posting">
                                                        <i class="cursor-pointer fas fa-dollar-sign text-secondary"
                                                            onclick="updatePosting({{ $posting['id'] }});"></i>
                                                    </a>
                                                    <span>
                                                        <i class="cursor-pointer fas fa-trash text-secondary"
                                                            onclick="deletePosting({{ $posting['id'] }});"></i>
                                                    </span>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmação -->
    <div class="modal fade" id="deletePostingModal" tabindex="-1" aria-labelledby="deletePostingModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deletePostingModalLabel">Confirmar desativação do Lançamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    Tem certeza de que deseja desativar esse Lançamento?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="confirmDeletePosting" class="btn btn-danger">Desativar</button>
                </div>
            </div>
        </div>
    </div>

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(function() {
            $("#startDate, #endDate").datepicker({
                dateFormat: "dd/mm/yy"
            });
        });

        function filterTableByDate() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;

            const url = new URL("{{ route('postings.show') }}");
            url.searchParams.set('start_date', startDate);
            url.searchParams.set('end_date', endDate);

            // Redirect to the filtered URL
            window.location.href = url;
        }

        function deletePosting(userId) {
            // Exibir o modal de confirmação
            var modal = document.getElementById('deletePostingModal');
            console.log(modal);
            var modalConfirmBtn = document.getElementById('confirmDeletePosting');
            modal.addEventListener('show.bs.modal', function() {
                modalConfirmBtn.addEventListener('click', function() {
                    // Fazer uma requisição para chamar o método 'destroy' do UserController
                    fetch('/posting-management/' + userId, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => {
                            if (response.ok) {
                                // Fechar o modal
                                var bootstrapModal = bootstrap.Modal.getInstance(modal);
                                bootstrapModal.hide();

                                // Atualizar a página ou fazer qualquer outra ação desejada
                                location.reload();
                            } else {
                                // Tratar o erro de acordo com sua necessidade
                                console.error('Erro ao excluir o lançamento');
                            }
                        })
                        .catch(error => {
                            console.error('Erro ao excluir o lançamento', error);
                        });
                });
            });
            var bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
        }

        function updatePosting(userId) {
            window.location.href = '/posting-management-update/' + userId;
        }
    </script>
@endsection
@endsection
