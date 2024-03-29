@extends('layouts.user_type.auth')

@section('content')
    <div>
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 mx-4">
                    <div class="card-header pb-0">
                        <div class="flex-row">
                            <div>
                                <h5 class="mb-0">Controle de Fechamento</h5>
                            </div>
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
                            <select class="form-control mb-3" id="deliverer" name="deliverer" aria-label="deliverer"
                                aria-describedby="deliverer">
                                <option value="">Entregador</option>
                                @foreach ($deliverers as $deliverer)
                                    <option value="{{ $deliverer->id }}">
                                        {{ $deliverer->name }}</option>
                                @endforeach
                            </select>
                            @error('deliverer')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                            @enderror
                            <button class="btn btn-primary" id="filter" onclick="filterTableByDate()">Filtrar</button>
                        </div>


                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0" style="overflow: auto;">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Nr Lançamento
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Tipo
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Entregador
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
                                            Insucessos
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Preço
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Data
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Total
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Pix
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($closures as $closure)
                                        <tr id="row{{ $closure['id'] }}"> <!-- Add an ID for each row -->
                                            <td class="ps-4">
                                                <p class="text-xs font-weight-bold mb-0">{{ $closure['id'] }}</p>
                                            </td>
                                            <td class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">{{ $closure['type'] }}</p>
                                            </td>
                                            <td class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">{{ $closure['deliverer'] }}</p>
                                            </td>
                                            <td class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">{{ $closure['priceRange'] }}
                                                </p>
                                            </td>
                                            <td class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">
                                                    {{ $closure['isNote'] == 1 ? 'Sim' : 'Não' }}</p>
                                            </td>
                                            <td class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">{{ $closure['quantity'] }}</p>
                                            </td>
                                            <td class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">{{ $closure['failures'] }}</p>
                                            </td>
                                            <td class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">
                                                    {{ 'R$ ' . number_format($closure['currentPrice'], 2, ',', '.') }}
                                                </p>
                                            </td>
                                            <td class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">{{ $closure['date'] }}</p>
                                            </td>
                                            <td class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">
                                                    {{ 'R$ ' . number_format($closure['total'], 2, ',', '.') }}</p>
                                            <td class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">{{ $closure['pix'] }}</p>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="pr-3">
                        <div class="d-flex justify-content-end m-3">
                            <h4>Valor Final:
                                {{ 'R$ ' . number_format($totalEnd, 2, ',', '.') }}</h4>
                        </div>
                    </div>
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
            $("#filter").prop("disabled", true);

            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const deliverer = document.getElementById('deliverer').value;

            const url = new URL("{{ route('closures') }}");
            url.searchParams.set('start_date', startDate);
            url.searchParams.set('end_date', endDate);
            url.searchParams.set('deliverer', deliverer);

            // Redirect to the filtered URL
            window.location.href = url;
        }
    </script>
@endsection
@endsection
