<?php

namespace App\Http\Controllers;

use App\Models\ProductBarcode;
use App\Models\PosCart;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StoreInventory;
use App\Models\Store; // Make sure to import the Store model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PosController extends Controller
{
    // Display the POS page and handle barcode search
    public function __construct()
    {
        $this->middleware('auth'); // Require authentication for all methods in this controller
    }

    public function chooseStore()
    {
        $stores = Store::all();
    
        // Check if there is only one store
        if ($stores->count() === 1) {
            return redirect()->route('pos.index', ['store_id' => $stores->first()->id]);
        }
    
        return view('pages.chooseStorePos', compact('stores'));
    }
    

    public function index(Request $request)
    {
        $productDetails = null;
        $storeInventoryDetails = null;
        $posCarts = null; // Initialize the variable for PosCart list
        $selectedAction = $request->input('action', 'pos'); 
    
        // Get store_id from the query string
        $store_id = $request->input('store_id');
    
        // Check if store_id is missing
        if (!$store_id) {
            return redirect()->route('pos.choose')->with('error', 'No SKU Specified');
        }
    
        if ($request->isMethod('post')) {
            // Validate the request
            $request->validate([
                'store_id' => 'required|integer',
                'barcode_number' => 'required|string',
            ]);
    
            // Attempt to get product details based on the barcode
            $result = $this->getProductDetails($request->barcode_number, $store_id);
    
            if ($result['error']) {
                return redirect()->route('pos.index', ['store_id' => $store_id])->with('error', $result['error']);
            }
    
            // Extract product and inventory details from the result
            $productDetails = $result['productDetails'];
            $storeInventoryDetails = $result['storeInventoryDetails'];
    
            // If the selected action is "POS", add the product to the PosCart
            if ($selectedAction === 'pos') {
                $this->addToPosCart($storeInventoryDetails);
            }
        }
    
        // Retrieve stores owned by the store owner
        $stores = Store::where('store_owner', auth()->user()->id)->get();
    
        // Retrieve the PosCart list filtered by store_id and user
        $userId = auth()->id(); // Get the authenticated user's ID
    
        $posCarts = PosCart::where('store_id', $store_id)
            ->where('user', $userId)
            ->get();
    
        Log::info('POS CARTTTTTTTTTTTTTTTT: ', $posCarts->toArray());
    
        return view('pages.pos', compact('productDetails', 'storeInventoryDetails', 'stores', 'posCarts', 'selectedAction'));
    }
    
    
    private function getProductDetails($barcode, $store_id)
    {
        // Search for the barcode number
        $barcodeDetails = ProductBarcode::where('barcode_number', $barcode)->first();
    
        // If barcode not found, return error
        if (!$barcodeDetails) {
            return ['error' => 'No items are detected on this barcode.'];
        }
    
        // Get the product using SKU from barcode
        $productDetails = Product::where('SKU', $barcodeDetails->product_sku)->first();
    
        // If product found, find store inventory filtered by store_id and SKU
        if ($productDetails) {
            $storeInventoryDetails = StoreInventory::where('SKU', $productDetails->SKU)
                ->where('store_id', $store_id)
                ->first();
    
            // Check if store inventory exists
            if (!$storeInventoryDetails) {
                return ['error' => 'No items are detected on this barcode.'];
            }
    
            return [
                'error' => null,
                'productDetails' => $productDetails,
                'storeInventoryDetails' => $storeInventoryDetails,
            ];
        }
    
        return ['error' => 'No items are detected on this barcode.'];
    }
    
    private function addToPosCart($storeInventoryDetails)
    {
        $existingCart = PosCart::where('product_sku', $storeInventoryDetails->SKU)
            ->where('user', auth()->id())
            ->where('store_id', $storeInventoryDetails->store_id)
            ->first();
    
        if ($existingCart) {
            // If it exists, increment the quantity
            $existingCart->quantity += 1; // Increment by 1
            $existingCart->orig_total = $existingCart->quantity * $existingCart->price; // Update total
            $existingCart->sub_total = max(0, $existingCart->orig_total - $existingCart->discount); // Update sub-total
            $existingCart->save(); // Save the updated cart
        } else {
            // If it doesn't exist, create a new PosCart entry
            $posCart = new PosCart();
            $posCart->product_sku = $storeInventoryDetails->SKU;
            $posCart->quantity = 1; // Start with a quantity of 1
            $posCart->price = $storeInventoryDetails->SPR;
            $posCart->date_added = now();
            $posCart->orig_total = $posCart->quantity * $posCart->price;
            $posCart->sub_total = max(0, $posCart->orig_total - 0); // Discount is defaulted to 0
            $posCart->product_bundle_id = $storeInventoryDetails->ProductID;
            $posCart->user = auth()->id();
            $posCart->discount = 0;
            $posCart->store_id = $storeInventoryDetails->store_id;
    
            $posCart->save(); // Save the new cart entry
        }
    }
    
    

    // Complete the sale
    public function completeSale(Request $request)
    {
        // Create a new sale record
        $sale = new Sale();
        $sale->customer_name = $request->customer_name;
        $sale->customer_number = $request->customer_number;
        $sale->total = $request->total;
        $sale->mode_of_payment = $request->mode_of_payment;
        $sale->amount_paid = $request->amount_paid;
        $sale->cx_change = $request->cx_change;
        $sale->cx_type = $request->cx_type;
        $sale->sale_made = $request->store_id;
        $sale->ordered_items = $request->ordered_items;
        $sale->processed_by = auth()->id(); // Set the ID of the user processing the sale
        $sale->date_of_transaction = now(); // Set the current timestamp
    
        // Save the sale record
        $sale->save();
    
        // Clear the PosCart for the user
        PosCart::where('user', auth()->id())->delete();
    
        // Return a JSON response with a success message
        return redirect()->route('pos.index', ['store_id' => $request->input('store_id')])
          ->with('success', 'Sale completed successfully.');return response()->json(['message' => 'Sale completed successfully.']);
    }    
}
