<?php

namespace App\Http\Controllers;

use App\Models\ProductBarcode;
use App\Models\PosCart;
use App\Models\Product;
use App\Models\Tally;
use App\Models\Sale;
use App\Models\User;
use App\Models\StoreInventory;
use App\Models\Store;
use App\Models\CxInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Mail\BadgePromotionMail;
use Illuminate\Support\Facades\Mail;
// use Illuminate\Support\Facades\Log;

class PosSaleController extends Controller
{
    // Display the POS page and handle barcode search
    public function __construct()
    {
        $this->middleware('auth'); // Require authentication for all methods in this controller
    }


    public function index(Request $request)
    {
        // Check if the authenticated user is 43
        if (auth()->id() !== 43) {
            // Redirect the user to the regular POS route if they are not user 43
            return redirect()->route('pos.index');
        }
    
        $productDetails = null;
        $posCarts = null; // Initialize the variable for PosCart list
        $selectedAction = $request->input('action', 'pos'); 
    
        if ($request->isMethod('post')) {
            // Validate the request
            $request->validate([
                'barcode_number' => 'required|string',
            ]);
    
            // Attempt to get product details based on the barcode
            $result = $this->getProductDetails($request->barcode_number);
    
            if ($result['error']) {
                return redirect()->route('posSale.index')->with('error', $result['error']);
            }
    
            // Extract product details from the result
            $productDetails = $result['productDetails'];
    
            // If the selected action is "POS", add the product to the PosCart
            if ($selectedAction === 'pos') {
                $barcode = ProductBarcode::where('barcode_number', $request->barcode_number)
                                         ->first();
                if ($barcode) {
                    if ($barcode->is_used) {
                        return redirect()->route('posSale.index')->with('error', 'Barcode has already been used.');
                    }
    
                    $barcode->is_used = true;
                    $barcode->save(); // Save the updated barcode
                } else {
                    return redirect()->route('posSale.index')->with('error', 'Barcode not found.');
                }
    
                // Pass the barcode_number when calling addToPosCart
                $this->addToPosCart($productDetails, $request->barcode_number);
            }
        }
    
        // Retrieve PosCart list filtered by user
        $userId = auth()->id(); // Get the authenticated user's ID
        $posCarts = PosCart::where('user', $userId)->get();
    
        return view('pages.posSale', compact('productDetails', 'posCarts', 'selectedAction'));
    }
    
       
    
    
    





    private function getProductDetails($barcode)
    {
        // Search for the barcode number
        $barcodeDetails = ProductBarcode::where('barcode_number', $barcode)->first();
    
        // If barcode not found, return error
        if (!$barcodeDetails) {
            return ['error' => 'No items are detected on this barcode.'];
        }
    
        // Get the product using SKU from barcode
        $productDetails = Product::where('SKU', $barcodeDetails->product_sku)->first();
    
        // If product found, return product details
        if ($productDetails) {
            return [
                'error' => null,
                'productDetails' => $productDetails,
            ];
        }
    
        return ['error' => 'No items are detected on this barcode.'];
    }
       
    
    





    private function addToPosCart($productDetails, $barcodeNumber)
    {
        $existingCart = PosCart::where('product_sku', $productDetails->SKU)
            ->where('user', auth()->id())
            ->first();  // Removed store_id check
    
        if ($existingCart) {
            // If it exists, increment the quantity
            $existingCart->quantity += 1; // Increment by 1
            $existingCart->orig_total = $existingCart->quantity * $existingCart->price; // Update total
            $existingCart->sub_total = max(0, $existingCart->orig_total - $existingCart->discount); // Update sub-total
    
            // Append the new barcode number to the barcode_numbers field
            $barcodeNumbers = json_decode($existingCart->barcode_numbers, true); // Decode the existing barcode numbers into an array
            $barcodeNumbers[] = $barcodeNumber; // Add the new barcode number
            $existingCart->barcode_numbers = json_encode($barcodeNumbers); // Encode back into JSON and save
    
            $existingCart->save(); // Save the updated cart
        } else {
            // If it doesn't exist, create a new PosCart entry
            $posCart = new PosCart();
            $posCart->product_sku = $productDetails->SKU;
            $posCart->quantity = 1; // Start with a quantity of 1
            $posCart->price = $productDetails->SRP; // Assuming the price is in the Product model
            $posCart->date_added = now();
            $posCart->orig_total = $posCart->quantity * $posCart->price;
            $posCart->sub_total = max(0, $posCart->orig_total - 0); // Discount is defaulted to 0
            $posCart->product_bundle_id = $productDetails->ProductID;
            $posCart->user = auth()->id();
            $posCart->discount = 0;
    
            // Removed store_id assignment
            $posCart->barcode_numbers = json_encode([$barcodeNumber]); // Initialize barcode_numbers as an array with the first barcode number
    
            $posCart->save(); // Save the new cart entry
        }
    }    
    
    
    
    





    public function completeSale(Request $request)
    {
        if ($request->mode_of_payment === 'Interest') {
            if ($request->customer_name || $request->customer_email || $request->customer_number) {
                $cxInfo = new CxInfo();
                $cxInfo->cx_name = $request->customer_name;
                $cxInfo->email = $request->customer_email;
                $cxInfo->phone_number = $request->customer_number;
                $cxInfo->cx_type = $request->cx_type;
                $cxInfo->interest = $request->interest; // Optionally, add interest if provided
                $cxInfo->remarks = $request->remarks; // Optionally, add remarks if provided
                $cxInfo->store_id = 7; // Optionally, add remarks if provided
    
                $cxInfo->save();
            }
    
            // Return a response indicating the sale was skipped but the customer info was saved
            return redirect()->route('posSale.index')
                ->with('success', 'Interest customer info saved successfully.');
        }
    
        // Proceed with the usual sale flow
    
        // Create a new sale record
        $sale = new Sale();
        $sale->customer_name = $request->customer_name;
        $sale->customer_number = $request->customer_number;
        $sale->customer_email = $request->customer_email; // Set customer_email
        $sale->total = $request->total;
        $sale->mode_of_payment = $request->mode_of_payment;
        $sale->amount_paid = $request->amount_paid;
        $sale->cx_change = $request->cx_change;
        $sale->cx_type = $request->cx_type;
        $sale->sale_made = 7;
        $sale->ordered_items = $request->ordered_items;
        $sale->ref_number_ewallet = $request->ref_number_ewallet;
        $sale->processed_by = auth()->id(); // Set the ID of the user processing the sale
        $sale->date_of_transaction = now(); // Set the current timestamp
        $sale->platform = $request->platform; // Set the platform (new field)
    
        // Save the sale record
        $sale->save();
    
        if ($request->customer_name || $request->customer_email || $request->customer_number) {
            $cxInfo = new CxInfo();
            $cxInfo->cx_name = $request->customer_name;
            $cxInfo->email = $request->customer_email;
            $cxInfo->phone_number = $request->customer_number;
            $cxInfo->cx_type = $request->cx_type;
            $cxInfo->interest = $request->interest; // Optionally, add interest if provided
            $cxInfo->remarks = $request->remarks; // Optionally, add remarks if provided
            $cxInfo->store_id = 7; // Optionally, add remarks if provided
    
            $cxInfo->save();
        }
    
        // Clear the PosCart for the user
        PosCart::where('user', auth()->id())->delete();
    
        // Decode ordered_items from JSON string to an array
        $orderedItems = json_decode($request->ordered_items, true);
    
        // Deduct the stock for each ordered item
        foreach ($orderedItems as $item) {
            $sku = $item['product_sku']; // Get the SKU from ordered items
            $quantity = $item['quantity']; // Assuming you have quantity in ordered items
    
            // Find the Product record (instead of StoreInventory)
            $product = Product::where('sku', $sku)->first();
    
            // If the product record exists, deduct the quantity
            if ($product) {
                $product->Stock -= $quantity; // Deduct the quantity (assuming Stock field exists in Product)
                $product->save(); // Save the changes
            }
        }
    
        // Update the store's total earnings
        $store = Store::find($request->store_id); // Find the store by ID
        if ($store) {
            $store->store_total_earnings += $sale->total; // Add the sale total to total earnings
            $store->save(); // Save the updated store record
        }
    
        return redirect()->route('posSale.index')
            ->with('success', 'Sale completed successfully.');
    }
    

    





    public function voidItem(Request $request)
    {
        // Validate the request
        $request->validate([
            'product_sku' => 'required|string',
        ]);
    
        $userId = auth()->id();
        $productSku = $request->input('product_sku');
        
        // Find the item in the PosCart
        $posCartItem = PosCart::where('product_sku', $productSku)
            ->where('user', $userId)
            ->first(); // Removed store_id condition
    
        if (!$posCartItem) {
            return redirect()->route('posSale.index')
                ->with('error', 'Item not found in the cart.');
        }
    
        // Update stock in Product (instead of StoreInventory)
        $product = Product::where('SKU', $productSku)
            ->first(); // Removed store_id condition
    
        if ($product) {
            $product->Stock += $posCartItem->quantity; // Re-add the stock
            $product->save();
        }
    
        // Loop through the barcode_numbers and set is_used to false for each barcode
        $barcodeNumbers = json_decode($posCartItem->barcode_numbers, true); // Decode the barcode_numbers array
        foreach ($barcodeNumbers as $barcodeNumber) {
            $barcode = ProductBarcode::where('barcode_number', $barcodeNumber)->first();
            
            if ($barcode) {
                $barcode->is_used = false; // Set is_used to false
                $barcode->save(); // Save the updated barcode
            }
        }
    
        // Remove the item from the PosCart
        $posCartItem->delete();
    
        return redirect()->route('posSale.index')
            ->with('success', 'Item voided successfully.');
    }
    
    
    






    public function applyDiscount(Request $request)
    {
        // Validate the request
        $request->validate([
            'product_sku' => 'required|string',
            'discount' => 'required|numeric|min:0',
        ]);
        
        // Find the item in the PosCart for the authenticated user (no store_id filter)
        $posCartItem = PosCart::where('product_sku', $request->product_sku)
            ->where('user', auth()->id())
            ->first();
        
        if (!$posCartItem) {
            return redirect()->route('posSale.index')
                ->with('error', 'Item not found in the cart.');
        }
        
        // Check if the discount exceeds the original total
        if ($request->discount >= $posCartItem->orig_total) {
            return redirect()->route('posSale.index')
                ->with('error', 'Discount cannot be bigger than the original total.');
        }
        
        // Update the discount
        $posCartItem->discount = $request->discount;
        $posCartItem->sub_total = max(0, $posCartItem->orig_total - $posCartItem->discount); // Recalculate sub_total
        $posCartItem->save();
        
        return redirect()->route('posSale.index')
            ->with('success', 'Discount applied successfully.');
    }        
}

