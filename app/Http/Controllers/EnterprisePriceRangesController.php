<?php

namespace App\Http\Controllers;

use App\Models\EnterprisePriceRange;
use App\Models\Enterprise;
use App\Models\PriceBand;
use App\Models\City;



use Illuminate\Support\Facades\Auth;

class EnterprisePriceRangesController extends Controller
{

    public function show()
    {
        $userAccess = Auth::user()->access;
        $enterprise_price_ranges = EnterprisePriceRange::where('status', 1)->get();
        $arrayEnterprisePriceRanges = [];

        foreach ($enterprise_price_ranges as $enterprise_price_range) {
            $arrayEnterprisePriceRanges[] = [
                'id' => $enterprise_price_range->id,
                'enterprise_id' => Enterprise::where('id', $enterprise_price_range->enterprise_id)->value('name'),
                'price_band_id' =>
                PriceBand::where('id', $enterprise_price_range->price_band_id)->value('value'),
                'city_id' => City::where('id', $enterprise_price_range->city_id)->value('name'),
                'status' => $enterprise_price_range->status,
                'created_at' => $enterprise_price_range->created_at
            ];
        }

        return view('enterprise_price_ranges/enterprise-price-range-management', ['enterprise_price_ranges' => $arrayEnterprisePriceRanges, 'userAccess' =>  $userAccess]);
    }

    public function showRemoved()
    {
        $userAccess = Auth::user()->access;
        $enterprise_price_ranges = EnterprisePriceRange::where('status', 0)->get();
        $arrayEnterprisePriceRanges = [];

        foreach ($enterprise_price_ranges as $enterprise_price_range) {
            $arrayEnterprisePriceRanges[] = [
                'id' => $enterprise_price_range->id,
                'enterprise_id' => Enterprise::where('id', $enterprise_price_range->enterprise_id)->value('name'),
                'price_band_id' =>
                PriceBand::where('id', $enterprise_price_range->price_band_id)->value('value'),
                'city_id' => City::where('id', $enterprise_price_range->city_id)->value('name'),
                'status' => $enterprise_price_range->status,
                'created_at' => $enterprise_price_range->created_at
            ];
        }

        return view('enterprise_price_ranges/enterprise-price-range-management-removed', ['enterprisePriceRanges' => $arrayEnterprisePriceRanges, 'userAccess' =>  $userAccess]);
    }

    public function create()
    {
        if (Auth::user() && Auth::user()->access !== 'admin') return redirect('/dashboard');

        $enterprises = Enterprise::where('status', 1)->select('id', 'name')->get();
        $priceBands = PriceBand::where('status', 1)->select('id', 'value')->get();
        $cities = City::where('status', 1)->select('id', 'name')->get();

        return view('enterprise_price_ranges.register', ['enterprises' => $enterprises, 'priceBands' =>  $priceBands, 'cities' =>  $cities]);
    }


    public function store()
    {
        // Verificar se o usuário tem acesso de administrador
        if (Auth::user() && Auth::user()->access !== 'admin')
            return redirect('/dashboard');


        $attributes = request()->validate([
            'enterprise_id' => ['required'],
            'price_band_id' => ['required'],
            'city_id' => ['required'],
        ]);

        $existingPriceRange = EnterprisePriceRange::where('enterprise_id', $attributes['enterprise_id'])
            ->where('price_band_id', $attributes['price_band_id'])
            ->where('city_id', $attributes['city_id'])
            ->first();


        if ($existingPriceRange) {
            return response()->json(['error' => 'Existe um atrelamento exatamente como esse, reative caso necessário ou verifique se já não cadastrou.']);
        }

        EnterprisePriceRange::create($attributes);
        return response()->json(['success' => 'Preço atrelado a empresa.']);
    }



    public function destroy($id)
    {
        EnterprisePriceRange::where('id', $id)
            ->update([
                'status' => false,
            ]);
    }

    public function recover($id)
    {
        EnterprisePriceRange::where('id', $id)
            ->update([
                'status' => true,
            ]);
    }
}
