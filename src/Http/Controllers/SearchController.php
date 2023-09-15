<?php

namespace Devzone\Pharmacy\Http\Controllers;

use Devzone\Pharmacy\Models\Product;
use Devzone\Pharmacy\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\Hospital\Patient;

class SearchController extends Controller
{
    public function searchProducts(Request $request)
    {
        $products = Product::from('products as p')
            ->when(!empty($request->name), function ($q) use ($request) {
                return $q->where('p.name', 'LIKE', '%' . $request->name . '%');
            })->select('p.id', 'p.name as text')
            ->limit(10)
            ->get();

        return response()->json($products);
    }

    public function searchPatient(Request $request)
    {
        $patients = Patient::where(function ($q) use ($request) {
            return $q->orWhere('name', 'LIKE', '%' . $request->name . '%')
                ->orWhere('mr_no', 'LIKE', '%' . $request->name . '%')
                ->orWhere('phone', 'LIKE', '%' . $request->name . '%');
        })->limit(10)->select('id','name as text')->get();

        return response()->json($patients);
    }

    public function searchSupplier(Request $request)
    {
        $suppliers = Supplier::where('status', 't')
            ->when(!empty($request->name), function ($q) use ($request) {
                return $q->where('name', 'LIKE', '%' . $request->name . '%');
            })
            ->select('id','name as text')
            ->limit(10)
            ->get();

        return response()->json($suppliers);
    }

}