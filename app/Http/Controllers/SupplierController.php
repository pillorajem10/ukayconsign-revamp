<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    // Display a listing of the suppliers
    public function index()
    {
        $suppliers = Supplier::all();
        return view('pages.suppliers', compact('suppliers'));
    }

    // Show the form for creating a new supplier
    public function create()
    {
        return view('pages.addSuppliers'); // Create a view for this
    }

    // Store a newly created supplier in storage
    public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:255',
        ]);

        Supplier::create($request->all());

        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully!');
    }

    // Show the form for editing the specified supplier
    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier')); // Create a view for this
    }

    // Update the specified supplier in storage
    public function update(Request $request, Supplier $supplier)
    {
        // Validate the request
        $request->validate([
            'supplier_name' => 'required|string|max:255',
        ]);
    
        // Update the supplier name
        $supplier->supplier_name = $request->supplier_name;
        $supplier->save();
    
        return response()->json(['success' => true]);
    }
      

    // Remove the specified supplier from storage
    public function destroy($supplier)
    {
        try {
            $supplier = Supplier::findOrFail($supplier); // This will throw an exception if not found
            $supplier->delete();
    
            return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully!');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('suppliers.index')->with('error', 'Supplier not found!');
        }
    }
    
}
