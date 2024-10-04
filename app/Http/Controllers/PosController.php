<?php

namespace App\Http\Controllers;

use App\Models\ProductBarcode;
use App\Models\Product;
use App\Models\StoreInventory;
use App\Models\Store; // Make sure to import the Store model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    // Display the POS page and handle barcode search
    public function index(Request $request)
    {
        $productDetails = null;
        $storeInventoryDetails = null;

        if ($request->isMethod('post')) {
            // Validate the request
            $request->validate([
                'store_id' => 'required|integer',
                'barcode_number' => 'required|string',
            ]);

            // Search for the barcode number
            $barcodeDetails = ProductBarcode::where('barcode_number', $request->barcode_number)->first();

            // If barcode not found, set message
            if (!$barcodeDetails) {
                $productDetails = ['message' => 'Barcode not found.'];
            } else {
                // Get the product using SKU from barcode
                $productDetails = Product::where('SKU', $barcodeDetails->product_sku)->first();

                // If product found, find store inventory filtered by store_id and SKU
                if ($productDetails) {
                    $storeInventoryDetails = StoreInventory::where('SKU', $productDetails->SKU)
                        ->where('store_id', $request->store_id)
                        ->get();
                } else {
                    $productDetails = ['message' => 'Product not found.'];
                }
            }
        }

        // Retrieve stores owned by the store owner
        $stores = Store::where('store_owner', auth()->user()->id)->get();

        return view('pages.pos', compact('productDetails', 'storeInventoryDetails', 'stores'));
    }
}

