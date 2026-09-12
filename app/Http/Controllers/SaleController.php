<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['user', 'invoice'])->latest();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', Carbon::parse($request->date));
        }

        $sales = $query->paginate(15)->withQueryString();

        $totalSalesToday = Sale::today()->where('payment_status', 'Paid')->sum('total');
        $ordersCountToday = Sale::today()->where('payment_status', 'Paid')->count();

        return view('sales.index', compact('sales', 'totalSalesToday', 'ordersCountToday'));
    }

    public function show(Sale $sale)
    {
        $sale->load(['items.menuItem', 'user', 'invoice', 'payments']);
        return view('sales.show', compact('sale'));
    }
}
