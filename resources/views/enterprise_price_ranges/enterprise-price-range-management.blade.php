@extends('layouts.user_type.auth')

@section('content')
    <div>
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 mx-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-row justify-content-between">
                            <div>
                                <h5 class="mb-0">Todos as Faixas Atreladas a Empresas</h5>
                            </div>
                            @if ($userAccess === 'admin')
                                <a href="{{ url('/enterprise-price-range-management-register') }}"
                                    class="btn bg-gradient-primary btn-sm" style="margin-bottom: 5px;">
                                    <span class="icon">
                                        <i class="fas fa-plus text-white"></i>
                                    </span>
                                    <span class="text d-none d-sm-inline">Cadastrar</span>
                                </a>
                                <br>
                                <a href="{{ url('/enterprise-price-range-management-removed') }}"
                                    class="btn bg-gradient-primary btn-sm" style="margin-bottom: 5px;">
                                    <span class="icon">
                                        <i class="fas fa-trash text-white"></i>
                                    </span>
                                    <span class="text d-none d-sm-inline">Buscar Removidos</span>
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
                                                Empresa
                                            </th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Faixa de Preço
                                            </th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Cidade
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
                                        @foreach ($enterprise_price_ranges as $enterprise_price_range)
                                            <tr>
                                                <td class="ps-4">
                                                    <p class="text-xs font-weight-bold mb-0">
                                                        {{ $enterprise_price_range['id'] }}</p>
                                                </td>
                                                <td class="text-center">
                                                    <p class="text-xs font-weight-bold mb-0">
                                                        {{ $enterprise_price_range['enterprise_id'] }}</p>
                                                </td>
                                                <td class="text-center">
                                                    <p class="text-xs font-weight-bold mb-0">
                                                        {{ 'R$' . number_format($enterprise_price_range['price_band_id'], 2, ',', '.') }}
                                                </td>
                                                <td class="text-center">
                                                    <p class="text-xs font-weight-bold mb-0">
                                                        {{ $enterprise_price_range['city_id'] }}
                                                    </p>
                                                </td>
                                                <td class="text-center">
                                                    <p class="text-xs font-weight-bold mb-0">
                                                        {{ $enterprise_price_range['status'] ? 'Ativo' : 'Inativo' }}</p>
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="text-secondary text-xs font-weight-bold">{{ $enterprise_price_range['created_at'] }}</span>
                                                </td>
                                                @if ($userAccess === 'admin')
                                                    <td class="text-center">
                                                        <span>
                                                            <i class="cursor-pointer fas fa-trash text-secondary"
                                                                onclick="deleteEnterprisePriceRange({{ $enterprise_price_range['id'] }});"></i>
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
        <div class="modal fade" id="deleteEnterprisePriceRangeModal" tabindex="-1"
            aria-labelledby="deleteEnterprisePriceRangeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteEnterprisePriceRangeModalLabel">Confirmar desativação do
                            Entregador</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        Tem certeza de que deseja desativar o Entregador?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" id="confirmDeleteEnterprisePriceRange"
                            class="btn btn-danger">Desativar</button>
                    </div>
                </div>
            </div>
        </div>


    @section('scripts')
        <script>
            function deleteEnterprisePriceRange(userId) {
                // Exibir o modal de confirmação
                var modal = document.getElementById('deleteEnterprisePriceRangeModal');
                var modalConfirmBtn = document.getElementById('confirmDeleteEnterprisePriceRange');
                modal.addEventListener('show.bs.modal', function() {
                    modalConfirmBtn.addEventListener('click', function() {
                        // Fazer uma requisição para chamar o método 'destroy' do UserController
                        fetch('/enterprise-price-range-management/' + userId, {
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

            function updateEnterprisePriceRange(userId) {
                window.location.href = '/enterprise-price-range-management-update/' + userId;
            }
        </script>
    @endsection
@endsection
