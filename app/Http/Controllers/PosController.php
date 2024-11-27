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

class PosController extends Controller
{
    // Display the POS page and handle barcode search
    public function __construct()
    {
        $this->middleware('auth'); // Require authentication for all methods in this controller
    }






    public function chooseStore()
    {
        // Get the authenticated user's ID
        $userId = Auth::id();
    
        // Fetch stores owned by the authenticated user
        $stores = Store::where('store_owner', $userId)->get();
    
        // Check if there are no stores
        if ($stores->isEmpty()) {
            return redirect()->route('home')->with('error', 'Kindly register your store first by ordering before you can use the POS.');
        }
    
        // Check if there is only one store
        if ($stores->count() === 1) {
            return redirect()->route('pos.index', ['store_id' => $stores->first()->id]);
        }
    
        return view('pages.chooseStorePos', compact('stores'));
    }      






    public function index(Request $request)
    {
        // Check if the authenticated user is 43
        if (auth()->id() === 43) {
            // Redirect the user to the 'posPrelove.index' route
            return redirect()->route('posSale.index');
        }
    
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
    
        // Check if the store_id belongs to the authenticated user
        $store = Store::where('id', $store_id)
                      ->where('store_owner', auth()->user()->id)
                      ->first();
    
        if (!$store) {
            return redirect()->route('home')->with('error', 'You don\'t have authority to access this POS');
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
                $barcode = ProductBarcode::where('barcode_number', $request->barcode_number)
                                         ->first();
                if ($barcode) {
                    if ($barcode->is_used) {
                        return redirect()->route('pos.index', ['store_id' => $store_id])->with('error', 'Barcode has already been used.');
                    }
    
                    $barcode->is_used = true;
                    $barcode->save(); // Save the updated barcode
                } else {
                    return redirect()->route('pos.index', ['store_id' => $store_id])->with('error', 'Barcode not found.');
                }
    
                // Pass the barcode_number when calling addToPosCart
                $this->addToPosCart($storeInventoryDetails, $request->barcode_number);
            }
        }
    
        // Retrieve stores owned by the store owner
        $stores = Store::where('store_owner', auth()->user()->id)->get();
    
        // Retrieve the PosCart list filtered by store_id and user
        $userId = auth()->id(); // Get the authenticated user's ID
    
        $posCarts = PosCart::where('store_id', $store_id)
            ->where('user', $userId)
            ->get();
    
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
    





    private function addToPosCart($storeInventoryDetails, $barcodeNumber)
    {
        // Get the product details based on SKU
        $product = Product::where('SKU', $storeInventoryDetails->SKU)->first();
    
        // Get the existing cart for the user and store
        $existingCart = PosCart::where('product_sku', $storeInventoryDetails->SKU)
            ->where('user', auth()->id())
            ->where('store_id', $storeInventoryDetails->store_id)
            ->first();
        
        if ($existingCart) {
            // If it exists, increment the quantity
            $existingCart->quantity += 1; // Increment by 1
            $existingCart->orig_total = $existingCart->quantity * $existingCart->price; // Update total
            $existingCart->sub_total = max(0, $existingCart->orig_total - $existingCart->discount); // Update sub-total
            
            // Calculate consign_total (quantity * consign_price)
            $existingCart->consign_total = $existingCart->quantity * $existingCart->consign_price; // Update consign_total
            
            // Append the new barcode number to the barcode_numbers field
            $barcodeNumbers = json_decode($existingCart->barcode_numbers, true); // Decode the existing barcode numbers into an array
            $barcodeNumbers[] = $barcodeNumber; // Add the new barcode number
            $existingCart->barcode_numbers = json_encode($barcodeNumbers); // Encode back into JSON and save
            
            // Save the updated cart
            $existingCart->save();
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
    
            // Get the consign_price from the Product model
            $posCart->consign_price = $product->Consign; // Assign consign price
            
            // Calculate consign_total (quantity * consign_price)
            $posCart->consign_total = $posCart->quantity * $posCart->consign_price; // Calculate consign_total
            
            // Initialize barcode_numbers as an array with the first barcode number
            $posCart->barcode_numbers = json_encode([$barcodeNumber]);
    
            // Save the new cart entry
            $posCart->save();
        }
    }
    
    
    
    





    public function completeSale(Request $request)
    {
        // If the payment method is "Interest", only save CxInfo and exit
        if ($request->mode_of_payment === 'Interest') {
            // Check if all customer details are empty or null before saving CxInfo
            if ($request->customer_name || $request->customer_email || $request->customer_number) {
                // Create a new CxInfo record with customer details
                $cxInfo = new CxInfo();
                $cxInfo->cx_name = $request->customer_name;
                $cxInfo->email = $request->customer_email;
                $cxInfo->phone_number = $request->customer_number;
                $cxInfo->cx_type = $request->cx_type;
                $cxInfo->interest = $request->interest; // Optionally, add interest if provided
                $cxInfo->remarks = $request->remarks; // Optionally, add remarks if provided
                $cxInfo->store_id = $request->store_id; // Associate with the store
                
                // Save the CxInfo record
                $cxInfo->save();
            }
        
            // Return a response indicating the sale was skipped but the customer info was saved
            return redirect()->route('pos.index', ['store_id' => $request->input('store_id')])
                ->with('success', 'Interest customer info saved successfully.');
        }

        // If mode_of_payment is not "Interest", proceed with the usual sale flow

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
        $sale->sale_made = $request->store_id;
        $sale->ordered_items = $request->ordered_items;
        $sale->ref_number_ewallet = $request->ref_number_ewallet;
        $sale->processed_by = auth()->id(); // Set the ID of the user processing the sale
        $sale->date_of_transaction = now(); // Set the current timestamp
        $sale->platform = $request->platform; // Set the platform (new field)
        
        // Save the sale record
        $sale->save();
        
        // Check if all customer details are empty or null before saving CxInfo
        if ($request->customer_name || $request->customer_email || $request->customer_number) {
            // Create a new CxInfo record with customer details
            $cxInfo = new CxInfo();
            $cxInfo->cx_name = $request->customer_name;
            $cxInfo->email = $request->customer_email;
            $cxInfo->phone_number = $request->customer_number;
            $cxInfo->cx_type = $request->cx_type;
            $cxInfo->interest = $request->interest; // Optionally, add interest if provided
            $cxInfo->remarks = $request->remarks; // Optionally, add remarks if provided
            $cxInfo->store_id = $request->store_id; // Associate with the store
            
            // Save the CxInfo record
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
        
            // Find the StoreInventory record
            $storeInventory = StoreInventory::where('sku', $sku)
                ->where('store_id', $request->store_id) // Ensure you're checking the correct store
                ->first();
        
            // If the inventory record exists, deduct the quantity
            if ($storeInventory) {
                $storeInventory->Stocks -= $quantity; // Deduct the quantity
                $storeInventory->save(); // Save the changes
            }
        }
        
        // Update the store's total earnings
        $store = Store::find($request->store_id); // Find the store by ID
        if ($store) {
            $store->store_total_earnings += $sale->total; // Add the sale total to total earnings
            $store->save(); // Save the updated store record
        
            // Check and update user badge
            $user = User::find($store->store_owner); // Find the user by store_owner ID
            if ($user) {
                $promoted = false; // Track if the user has been promoted
        
                if ($store->store_total_earnings >= 30000 && $user->badge === 'Silver') {
                    $user->badge = 'Gold'; // Update badge to Gold
                    $promoted = true;
                } elseif ($store->store_total_earnings >= 50000 && $user->badge === 'Gold') {
                    $user->badge = 'Platinum'; // Update badge to Platinum
                    $promoted = true;
                }
        
                if ($promoted) {
                    $user->save(); // Save the updated user
                    Mail::to($user->email)->send(new BadgePromotionMail($user)); // Send email notification
                }
            }
        }        
        
        // Return a JSON response with a success message
        return redirect()->route('pos.index', ['store_id' => $request->input('store_id')])
            ->with('success', 'Sale completed successfully.');
    }

    





    public function voidItem(Request $request)
    {
        // Validate the request
        $request->validate([
            'product_sku' => 'required|string',
            'store_id' => 'required|integer',
        ]);
    
        $userId = auth()->id();
        $storeId = $request->input('store_id');
        $productSku = $request->input('product_sku');
    
        // Find the item in the PosCart
        $posCartItem = PosCart::where('product_sku', $productSku)
            ->where('user', $userId)
            ->where('store_id', $storeId)
            ->first();
    
        if (!$posCartItem) {
            return redirect()->route('pos.index', ['store_id' => $storeId])
                ->with('error', 'Item not found in the cart.');
        }
    
        // Update stock in StoreInventory
        $storeInventory = StoreInventory::where('SKU', $productSku)
            ->where('store_id', $storeId)
            ->first();
    
        if ($storeInventory) {
            $storeInventory->Stocks += $posCartItem->quantity; // Re-add the stock
            $storeInventory->save();
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
    
        return redirect()->route('pos.index', ['store_id' => $storeId])
            ->with('success', 'Item voided successfully.');
    }
    






    public function applyDiscount(Request $request)
    {
        // Validate the request
        $request->validate([
            'product_sku' => 'required|string',
            'store_id' => 'required|integer',
            'discount' => 'required|numeric|min:0',
        ]);
    
        // Find the item in the PosCart
        $posCartItem = PosCart::where('product_sku', $request->product_sku)
            ->where('user', auth()->id())
            ->where('store_id', $request->store_id)
            ->first();
    
        if (!$posCartItem) {
            return redirect()->route('pos.index', ['store_id' => $request->store_id])
                ->with('error', 'Item not found in the cart.');
        }
    
        // Check if the discount exceeds the original total
        if ($request->discount >= $posCartItem->orig_total) {
            return redirect()->route('pos.index', ['store_id' => $request->store_id])
                ->with('error', 'Discount cannot be bigger than the original total.');
        }
    
        // Update the discount
        $posCartItem->discount = $request->discount;
        $posCartItem->sub_total = max(0, $posCartItem->orig_total - $posCartItem->discount); // Recalculate sub_total
        $posCartItem->save();
    
        return redirect()->route('pos.index', ['store_id' => $request->store_id])
            ->with('success', 'Discount applied successfully.');
    }    
}

