@extends('layouts.user_type.auth')

@section('content')
    <div>
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 mx-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-row justify-content-between">
                            <div>
                                <h5 class="mb-0">Todos os Entregadores</h5>
                            </div>
                            @if ($userAccess === 'admin')
                                <a href="{{ url('/deliverer-management-register') }}" class="btn bg-gradient-primary btn-sm"
                                    style="margin-bottom: 5px;">
                                    <span class="icon">
                                        <i class="fas fa-plus text-white"></i>
                                    </span>
                                    <span class="text d-none d-sm-inline">Cadastrar Entregador</span>
                                </a>
                                <br>
                                <a href="{{ url('/deliverer-management-removed') }}" class="btn bg-gradient-primary btn-sm"
                                    style="margin-bottom: 5px;">
                                    <span class="icon">
                                        <i class="fas fa-trash text-white"></i>
                                    </span>
                                    <span class="text d-none d-sm-inline">Entregadores Removidos</span>
                                </a>
                            @endif
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0" style="overflow: auto;">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                ID
                                            </th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Nome
                                            </th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Pix
                                            </th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Identificação
                                            </th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Status
                                            </th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Criação
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
                                        @foreach ($deliverers as $deliverer)
                                            <tr>
                                                <td class="ps-4">
                                                    <p class="text-xs font-weight-bold mb-0">{{ $deliverer['id'] }}</p>
                                                </td>
                                                <td class="text-center">
                                                    <p class="text-xs font-weight-bold mb-0">{{ $deliverer['name'] }}</p>
                                                </td>
                                                <td class="text-center">
                                                    <p class="text-xs font-weight-bold mb-0">{{ $deliverer['pix'] }}</p>
                                                </td>
                                                <td class="text-center">
                                                    <p class="text-xs font-weight-bold mb-0">{{ $deliverer['cnpj_or_cpf'] }}
                                                    </p>
                                                </td>
                                                <td class="text-center">
                                                    <p class="text-xs font-weight-bold mb-0">
                                                        {{ $deliverer['status'] ? 'Ativo' : 'Inativo' }}</p>
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="text-secondary text-xs font-weight-bold">{{ $deliverer['created_at'] }}</span>
                                                </td>
                                                @if ($userAccess === 'admin')
                                                    <td class="text-center">
                                                        <a class="mx-3" data-bs-toggle="tooltip"
                                                            data-bs-original-title="Edit deliverer">
                                                            <i class="cursor-pointer fas fa-truck text-secondary"
                                                                onclick="updateDeliverer({{ $deliverer['id'] }});"></i>
                                                        </a>
                                                        <span>
                                                            <i class="cursor-pointer fas fa-trash text-secondary"
                                                                onclick="deleteDeliverer({{ $deliverer['id'] }});"></i>
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
        <div class="modal fade" id="deleteDelivererModal" tabindex="-1" aria-labelledby="deleteDelivererModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteDelivererModalLabel">Confirmar desativação do Entregador</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        Tem certeza de que deseja desativar o Entregador?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" id="confirmDeleteDeliverer" class="btn btn-danger">Desativar</button>
                    </div>
                </div>
            </div>
        </div>


    @section('scripts')
        <script>
            function deleteDeliverer(userId) {
                // Exibir o modal de confirmação
                var modal = document.getElementById('deleteDelivererModal');
                var modalConfirmBtn = document.getElementById('confirmDeleteDeliverer');
                modal.addEventListener('show.bs.modal', function() {
                    modalConfirmBtn.addEventListener('click', function() {
                        // Fazer uma requisição para chamar o método 'destroy' do UserController
                        fetch('/deliverer-management/' + userId, {
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
                                    console.error('Erro ao excluir o cidade');
                                }
                            })
                            .catch(error => {
                                console.error('Erro ao excluir o cidade', error);
                            });
                    });
                });
                var bootstrapModal = new bootstrap.Modal(modal);
                bootstrapModal.show();
            }

            function updateDeliverer(userId) {
                window.location.href = '/deliverer-management-update/' + userId;
            }
        </script>
    @endsection
@endsection
