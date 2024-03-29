<?php

namespace App\Http\Controllers;

use App\Models\Posting;
use App\Models\Deliverer;
use App\Models\User;
use App\Models\EnterprisePriceRange;
use App\Models\Enterprise;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class PostingsController extends Controller
{

    private function convertDate($date)
    {
        // Set the timezone to Brasília
        $timezoneBrasilia = new \DateTimeZone('America/Sao_Paulo');
        Carbon::setTestNow(Carbon::now($timezoneBrasilia));

        if ($date) {
            return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
        } else {
            return Carbon::now()->toDateString();
        }
    }

    public function show(Request $request)
    {
        $deliverers = Deliverer::where('status', 1)->select('id', 'name')->orderby('name')->get();
        $userAccess = Auth::user()->access;
        $postingsQuery = Posting::query();

        $startDate = $this->convertDate($request->input('start_date'));
        $endDate = $this->convertDate($request->input('end_date'));
        $deliverer = $request->deliverer;


        if ($startDate && $endDate) {
            $postingsQuery->whereBetween('date', [$startDate, $endDate]);
        }

        if ($deliverer) {
            $postingsQuery->where('deliverer_id', $deliverer);
        }

        $postings = $postingsQuery->where('removed', 0)->get();
        $arrayPostings = [];

        foreach ($postings as $posting) {
            $delivererName = Deliverer::where('id', $posting->deliverer_id)->value('name');
            $userName = User::where('id', $posting->user_id)->value('name');
            $priceRangeEnterprise = EnterprisePriceRange::join('enterprises', 'enterprise_price_ranges.enterprise_id', '=', 'enterprises.id')
                ->where('enterprise_price_ranges.id', $posting->enterprise_price_range_id)
                ->value('enterprises.name');
            $priceRangeCity =
                EnterprisePriceRange::join('cities', 'enterprise_price_ranges.city_id', '=', 'cities.id')
                ->where('enterprise_price_ranges.id', $posting->enterprise_price_range_id)
                ->value('cities.name');

            $arrayPostings[] = [
                'id' => $posting->id,
                'deliverer' => $delivererName,
                'user' => $userName,
                'priceRange' => $posting->enterprise_price_range_id ? $priceRangeEnterprise . ' - ' . $priceRangeCity : $posting->enterprise,
                'isNote' => $posting->isNote,
                'quantity' => $posting->quantity,
                'currentPrice' => $posting->currentPrice,
                'type' => $posting->type,
                'date' => $posting->date,
            ];
        }

        return view('postings.posting-management', ['postings' => $arrayPostings, 'userAccess' =>  $userAccess, 'deliverers' => $deliverers]);
    }


    public function create()
    {
        if (Auth::user() && Auth::user()->access !== 'admin') {
            return redirect('/dashboard');
        }

        $deliverers = Deliverer::where('status', 1)->select('id', 'name')->orderBy('name')->get();
        $enterprises =
            Enterprise::where('status', 1)->select('id', 'name')->orderBy('name')->get();
        $user = Auth::user();
        $enterprisePriceRanges = EnterprisePriceRange::select(
            'enterprise_price_ranges.id',
            DB::raw("CONCAT(enterprises.name, ' - ', cities.name, ' - R$', FORMAT(price_bands.value, 2)) as formatted_data")
        )
            ->leftJoin('enterprises', 'enterprise_price_ranges.enterprise_id', '=', 'enterprises.id')
            ->leftJoin('price_bands', 'enterprise_price_ranges.price_band_id', '=', 'price_bands.id')
            ->leftJoin('cities', 'enterprise_price_ranges.city_id', '=', 'cities.id')
            ->where('enterprise_price_ranges.status', 1)
            ->orderBy('enterprises.name')
            ->orderBy('cities.name')
            ->get();


        return view('postings.register', ['deliverers' => $deliverers, 'user' =>  $user, 'enterprisePriceRanges' => $enterprisePriceRanges, 'enterprises' => $enterprises]);
    }


    public function store()
    {
        try {
            if (Auth::user() && Auth::user()->access !== 'admin') {
                return redirect('/dashboard');
            }

            // Validação dos atributos
            $attributes = request()->validate([
                'deliverer' => ['required'],
                'user' => ['required'],
                'enterprisePriceRange' => [],
                'isNote' => [],
                'quantity' => ['required', 'numeric'],
                'type' => ['required'],
                'date' => ['required'],
                'fixedValue' => [],
                'enterprise' => [],
            ]);


            $price = EnterprisePriceRange::leftJoin('price_bands as pb', 'enterprise_price_ranges.price_band_id', '=',  'pb.id')
                ->where('enterprise_price_ranges.id', $attributes['enterprisePriceRange'])
                ->pluck('pb.value')
                ->first();

            if (!strpos($attributes['date'], '-')) {
                $attributes['date'] = Carbon::createFromFormat('d/m/Y', $attributes['date'])->format('Y-m-d');
            }

            if (isset($attributes['fixedValue'])) {
                $attributes['fixedValue'] = str_replace(',', '.', $attributes['fixedValue']);
            }

            if ($attributes['type'] == 'insucesso') {
                $existingShipment = Posting::where('deliverer_id', $attributes['deliverer'])->where('type', 'carregamento')->where('date', $attributes['date'])->first();
                $existingFailure = Posting::where('deliverer_id', $attributes['deliverer'])->where('type', 'insucesso')->where('date', $attributes['date'])->where('enterprise_price_range_id', $attributes['enterprisePriceRange'])->first();

                if (empty($existingShipment)) return response()->json(['error' => 'Você não pode lançar um insucesso sem um carregamento para essa data.']);

                if (!empty($existingFailure)) return response()->json(['error' => 'Já existe um insucesso para esse carregamento, edite ou remova na tela de lançamento.']);
            }

            if (isset($attributes['fixedValue'])) {
                $data = [
                    'deliverer_id' => $attributes['deliverer'],
                    'user_id' => $attributes['user'],
                    'isNote' => $attributes['isNote'] == 'true' ? 1 : 0,
                    'quantity' => $attributes['quantity'],
                    'type' => $attributes['type'],
                    'date' => $attributes['date'],
                    'currentPrice' => $attributes['fixedValue'],
                    'enterprise' => $attributes['enterprise']
                ];


                Posting::create($data);
                return response()->json(['success' => 'Lançamento Cadastrado.']);
            }

            $data = [
                'deliverer_id' => $attributes['deliverer'],
                'user_id' => $attributes['user'],
                'enterprise_price_range_id' => $attributes['enterprisePriceRange'],
                'isNote' => $attributes['isNote'] == 'true' ? 1 : 0,
                'quantity' => $attributes['quantity'],
                'type' => $attributes['type'],
                'date' => $attributes['date'],
                'currentPrice' => $price,
            ];


            Posting::create($data);
            return response()->json(['success' => 'Lançamento Cadastrado.']);
        } catch (ValidationException) {
            return response()->json(['error' => 'Verifique se preencheu todos os campos corretamente.']);
        }
    }

    public function preventDuplicated(Request $request)
    {
        $enterprisePriceRange = $request->enterprisePriceRange;
        $deliverer = $request->deliverer;
        $date = $request->date;
        $type = $request->type;

        if ($request->update) {
            $existingPosting = Posting::where('enterprise_price_range_id', $enterprisePriceRange)
                ->where('deliverer_id', $deliverer)
                ->where('date', $date)
                ->where('type', $type)
                ->get();

            return response()->json(['duplicated' => !empty($existingPosting) && count($existingPosting) > 1]);
        }

        $existingPosting = Posting::where('enterprise_price_range_id', $enterprisePriceRange)
            ->where('deliverer_id', $deliverer)
            ->where('date', $date)
            ->where('type', $type)
            ->first();

        return response()->json(['duplicated' => !empty($existingPosting)]);
    }


    public function update($id)
    {
        if (Auth::user() && Auth::user()->access !== 'admin') {
            return redirect('/dashboard');
        }

        $postingEdit = Posting::where('id', $id)->first();
        $deliverers = Deliverer::where('status', 1)->select('id', 'name')->orderBy('name')->get();
        $enterprises =
            Enterprise::where('status', 1)->select('id', 'name')->orderBy('name')->get();
        $user = Auth::user();
        $enterprisePriceRanges = EnterprisePriceRange::select(
            'enterprise_price_ranges.id',
            DB::raw("CONCAT(enterprises.name, ' - ', cities.name, ' - R$', FORMAT(price_bands.value, 2)) as formatted_data")
        )
            ->leftJoin('enterprises', 'enterprise_price_ranges.enterprise_id', '=', 'enterprises.id')
            ->leftJoin('price_bands', 'enterprise_price_ranges.price_band_id', '=', 'price_bands.id')
            ->leftJoin('cities', 'enterprise_price_ranges.city_id', '=', 'cities.id')
            ->where('enterprise_price_ranges.status', 1)
            ->orderBy('enterprises.name')
            ->orderBy('cities.name')
            ->get();

        return view('postings.posting-management-update', ['deliverers' => $deliverers, 'user' =>  $user, 'enterprisePriceRanges' => $enterprisePriceRanges, 'postingEdit' => $postingEdit, 'enterprises' => $enterprises]);
    }
    public function updatePosting($id)
    {
        try {
            $attributes = request()->validate([
                'deliverer' => ['required'],
                'user' => ['required'],
                'enterprisePriceRange' => [],
                'isNote' => [],
                'quantity' => ['required', 'numeric'],
                'type' => ['required'],
                'date' => ['required'],
                'fixedValue' => ['numeric'],
                'enterprise' => [],
            ]);

            $price = EnterprisePriceRange::leftJoin('price_bands as pb', 'enterprise_price_ranges.price_band_id', '=',  'pb.id')
                ->where('enterprise_price_ranges.id', $attributes['enterprisePriceRange'])
                ->pluck('pb.value')
                ->first();

            if (!strpos($attributes['date'], '-')) {
                $attributes['date'] = Carbon::createFromFormat('d/m/Y', $attributes['date'])->format('Y-m-d');
            }

            if (isset($attributes['fixedValue'])) {
                $attributes['fixedValue'] = str_replace(',', '.', $attributes['fixedValue']);
                Posting::where('id', $id)->update([
                    'deliverer_id' => $attributes['deliverer'],
                    'user_id' => $attributes['user'],
                    'enterprise' => $attributes['enterprise'],
                    'isNote' => $attributes['isNote'] == 'true' ? 1 : 0,
                    'quantity' => $attributes['quantity'],
                    'type' => $attributes['type'],
                    'date' => $attributes['date'],
                    'currentPrice' => $attributes['fixedValue'],
                ]);

                return response()->json(['success' => 'Alteração Realizada.']);
            }

            Posting::where('id', $id)->update([
                'deliverer_id' => $attributes['deliverer'],
                'user_id' => $attributes['user'],
                'enterprise_price_range_id' => $attributes['enterprisePriceRange'],
                'isNote' => $attributes['isNote'] == 'true' ? 1 : 0,
                'quantity' => $attributes['quantity'],
                'type' => $attributes['type'],
                'date' => $attributes['date'],
                'currentPrice' => $price,
            ]);

            return response()->json(['success' => 'Alteração Realizada.']);
        } catch (ValidationException) {
            return response()->json(['error' => 'Verifique se preencheu todos os campos corretamente.']);
        }
    }


    public function destroy($id)
    {
        $user = Auth::user()->id;
        Posting::where('id', $id)
            ->update([
                'removed' => true,
                'removed_id' => $user
            ]);
    }
}
