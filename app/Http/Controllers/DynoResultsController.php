<?php

namespace App\Http\Controllers;

use App\Models\DynoResult;
use Illuminate\Http\Request;

class DynoResultsController extends Controller
{
    public function index(Request $request)
    {
        $q = DynoResult::approved()
            ->with('user:id,name')
            ->orderByDesc('created_at');

        if ($request->filled('make')) {
            $q->where('vehicle_label', 'like', $request->query('make') . '%');
        }

        $results = $q->paginate(12)->withQueryString();

        return view('marketing.results', ['results' => $results]);
    }
}
