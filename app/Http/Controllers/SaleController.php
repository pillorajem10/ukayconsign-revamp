<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Store;
use App\Models\Product;
use App\Models\ProductBarcode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Require authentication for all methods in this controller
    }

    
    public function index(Request $request)
    {
        // Get store_id from the query string
        $storeId = $request->input('store_id');
    
        // Check if the authenticated user is the owner of the store
        $store = Store::where('id', $storeId)
            ->where('store_owner', auth()->user()->id)
            ->first();
    
        if (!$store) {
            return redirect()->route('dashboard')->with('error', 'You don\'t have authority to access this sales list');
        }
    
        // Initialize the sales query
        $salesQuery = Sale::where('sale_made', $storeId);
    
        // Handle filtering by date if provided
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = \Carbon\Carbon::parse($request->input('start_date'))->startOfDay();
            $endDate = \Carbon\Carbon::parse($request->input('end_date'))->endOfDay();
            $salesQuery->whereBetween('date_of_transaction', [$startDate, $endDate]);
        }
    
        // Order the sales by createdAt in descending order (most recent first)
        $sales = $salesQuery->orderBy('createdAt', 'desc')->paginate(10); // Display 10 sales per page
    
        return view('pages.sales', compact('sales', 'storeId'));
    }  
    
    public function voidSale($saleId)
    {
        $sale = Sale::find($saleId);
        
        if (!$sale) {
            return redirect()->back()->with('error', 'Sale not found.');
        }
    
        $store = Store::find(7);
        
        if (!$store) {
            return redirect()->back()->with('error', 'Store not found.');
        }
    
        // Log the store details to check the found store
        Log::debug('Store Details:', $store->toArray());
        
        // Deduct the sale total from the store's total earnings
        $store->store_total_earnings -= $sale->total;
        $store->save(); // Save the updated store
        
        // Log the updated store total earnings
        Log::debug('Updated Store Earnings:', ['store_total_earnings' => $store->store_total_earnings]);
        
        // Decode the ordered_items JSON to get an array of items
        $orderedItems = json_decode($sale->ordered_items);
        
        // Log the ordered items to inspect their structure
        Log::debug('Ordered Items:', $orderedItems);
        
        // Loop through each ordered item and update the product stock and barcodes
        foreach ($orderedItems as $item) {
            // Find the product by SKU
            $product = Product::where('SKU', $item->product_sku)->first();
            
            if ($product) {
                // Log the product details
                Log::debug('Product Found:', $product->toArray());
    
                // Add the quantity of the item back to the product stock
                $product->Stock += $item->quantity;
                $product->save(); // Save the updated stock
    
                // Log the updated product stock
                Log::debug('Updated Product Stock:', ['SKU' => $item->product_sku, 'Stock' => $product->Stock]);
    
                // Process barcodes for the current item
                $barcodeNumbers = json_decode($item->barcode_numbers);
    
                // Loop through each barcode number and update the 'is_used' field
                foreach ($barcodeNumbers as $barcodeNumber) {
                    // Find the barcode in the product_barcodes table
                    $productBarcode = ProductBarcode::where('barcode_number', $barcodeNumber)
                        ->where('product_sku', $item->product_sku)
                        ->first();
    
                    if ($productBarcode) {
                        // Log the barcode details
                        Log::debug('Barcode Found:', $productBarcode->toArray());
    
                        // Set the 'is_used' flag to false (or 0)
                        $productBarcode->is_used = false;
                        $productBarcode->save(); // Save the updated barcode
    
                        // Log the barcode update
                        Log::debug('Updated Barcode:', ['barcode_number' => $barcodeNumber, 'is_used' => $productBarcode->is_used]);
                    }
                }
            }
        }
    
        // Mark the sale as voided
        $sale->is_voided = true;
        $sale->save(); // Save the voided sale
        
        // Log the sale voided status
        Log::debug('Sale Voided:', ['sale_id' => $sale->id, 'is_voided' => $sale->is_voided]);
    
        return redirect()->back()->with('success', 'Sale has been voided, stock updated, barcodes reset, and store earnings adjusted.');
    }     
}
