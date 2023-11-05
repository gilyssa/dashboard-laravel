<?php

namespace App\Http\Controllers;

use App\Models\Posting;
use App\Models\Deliverer;
use App\Models\User;
use App\Models\EnterprisePriceRange;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class ClosuresController extends Controller
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
        $deliverers = Deliverer::where('status', 1)->select('id', 'name')->get();
        $userAccess = Auth::user()->access;
        $closuresQuery = Posting::query();

        $startDate = $this->convertDate($request->input('start_date'));
        $endDate = $this->convertDate($request->input('end_date'));


        if ($startDate && $endDate) {
            $closuresQuery->whereBetween('date', [$startDate, $endDate]);
        }

        $closures = $closuresQuery->where('removed', 0)->get();
        $arrayClosures = [];

        foreach ($closures as $posting) {
            $delivererName = Deliverer::where('id', $posting->deliverer_id)->value('name');
            $delivererPix = Deliverer::where('id', $posting->deliverer_id)->value('pix');
            $priceRangeEnterprise = EnterprisePriceRange::join('enterprises', 'enterprise_price_ranges.enterprise_id', '=', 'enterprises.id')
                ->where('enterprise_price_ranges.id', $posting->enterprise_price_range_id)
                ->value('enterprises.name');
            $priceRangeCity =
                EnterprisePriceRange::join('cities', 'enterprise_price_ranges.city_id', '=', 'cities.id')
                ->where('enterprise_price_ranges.id', $posting->enterprise_price_range_id)
                ->value('cities.name');

            $arrayClosures[] = [
                'id' => $posting->id,
                'deliverer' => $delivererName,
                'pix' => $delivererPix,
                'priceRange' => $priceRangeEnterprise . ' - ' . $priceRangeCity,
                'isNote' => $posting->isNote,
                'quantity' => $posting->quantity,
                'currentPrice' => $posting->currentPrice,
                'date' => $posting->date,
            ];
        }

        return view('closures', ['closures' => $arrayClosures, 'userAccess' =>  $userAccess, 'deliverers' => $deliverers]);
    }
}
