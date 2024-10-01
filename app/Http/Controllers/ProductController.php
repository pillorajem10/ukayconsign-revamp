<?php

namespace App\Http\Controllers;
use App\Models\Cart;
use App\Models\Product; // Import the Product model
use App\Models\ProductBarcode; // Import the Product model
use App\Models\Batch; // Import the Product model
use App\Models\ReceivedProduct;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Picqer\Barcode\BarcodeGeneratorPNG; // For PNG output


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /*
    public function __construct()
    {
        $this->middleware('auth'); // Require authentication for all methods in this controller
    }
    */

    public function index(Request $request)
    {
        $search = $request->input('search');
    
        // Check if the user is authenticated
        if (Auth::check()) {
            // Fetch cart items for the authenticated user
            $carts = Cart::with('product')->where('user_id', Auth::id())->get();
    
            // Check if the cart is empty
            if ($carts->isEmpty()) {
                $cartMessage = "Your cart is empty.";
            } else {
                $cartMessage = null; // Or set it to an empty string if you prefer
            }
        } else {
            $carts = collect(); // Create an empty collection
            $cartMessage = "Login first to access cart.";
        }
    
        // Paginate the results first, then group them by Bundle
        $products = Product::when($search, function ($query) use ($search) {
            return $query->where('ProductID', 'like', '%' . $search . '%');
        })
        ->paginate(10);
    
        // Group the paginated items by Bundle
        $groupedProducts = $products->getCollection()->groupBy('Bundle');
        $products->setCollection($groupedProducts); // Replace the collection with grouped one
    
        return view('pages.home', compact('products', 'search', 'carts', 'cartMessage')); // Pass the cart message
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::all(); // Fetch all suppliers
        return view('pages.addProduct', compact('suppliers')); // Pass suppliers to the view
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'SKU' => 'required|string|max:50|unique:products,SKU',
            'Bundle' => 'nullable|string|max:255',
            'Type' => 'nullable|string|max:50',
            'Style' => 'nullable|string|max:50',
            'Color' => 'nullable|string|max:50',
            'Gender' => 'nullable|string|max:50',
            'Category' => 'nullable|string|max:50',
            'Bundle_Qty' => 'nullable|integer',
            'Consign' => 'nullable|numeric',
            'Cash' => 'nullable|numeric',
            'SRP' => 'nullable|string|max:50',
            'maxSRP' => 'nullable|string|max:50',
            'PotentialProfit' => 'nullable|numeric',
            'Date' => 'nullable|date',
            'Cost' => 'nullable|numeric',
            'Stock' => 'nullable|integer',
            'Supplier' => 'nullable|string|max:255',
            'Image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'Secondary_Img' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'Img_color' => 'nullable|string|max:255',
            'is_hidden' => 'nullable|boolean',
            'Batch_number' => 'nullable|string|max:255',
            'Bale' => 'nullable|string|max:255',
            'createdAt' => 'nullable|date',
            'batches' => 'nullable|text',
        ], [
            'SKU.unique' => "{$request->SKU} is already existed in products." // Custom error message
        ]);
    
        // Create the product
        $product = new Product($validatedData);
    
        // Generate ProductID from concatenation
        $product->ProductID = trim("{$request->Type} {$request->Style} {$request->Color} {$request->Gender} {$request->Category}");
    
        // Save the product first to get the SKU for batch
        $product->save();
    
        // Generate batch number
        $date = now()->format('mdy'); // Format: MMDDYY
        $bale = $request->Bale ?? 'Unknown'; // Get Bale from request or default to 'Unknown'
    
        // Adjust batch base to include SKU
        $batchBase = "{$date}-{$product->SKU}-{$bale}";
    
        // Count existing batches with exact SKU and date
        $existingBatchCount = Batch::where('Batch_number', 'like', "{$date}-{$product->SKU}-{$bale}-%")->count();
    
        $batchSuffix = str_pad($existingBatchCount + 1, 2, '0', STR_PAD_LEFT); // Pad with zeros
        $batchNumber = "{$batchBase}-{$batchSuffix}";
    
        // Create the batch entry
        Batch::create([
            'SKU' => $product->SKU,
            'Bundle' => $product->Bundle,
            'ProductID' => $product->ProductID,
            'Type' => $product->Type,
            'Style' => $product->Style,
            'Color' => $product->Color,
            'Gender' => $product->Gender,
            'Category' => $product->Category,
            'Bundle_Qty' => $product->Bundle_Qty,
            'Consign' => $product->Consign,
            'SRP' => $product->SRP,
            'maxSRP' => $product->maxSRP,
            'PotentialProfit' => $product->PotentialProfit,
            'Cost' => $product->Cost,
            'Stock' => $product->Stock,
            'Supplier' => $product->Supplier,
            'Img_color' => $product->Img_color,
            'Date' => now(), // Use current date
            'Bale' => $bale,
            'Batch_number' => $batchNumber,
        ]);
    
        // Generate barcodes
        for ($i = 0; $i < $product->Stock; $i++) {
            $barcodeNumber = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT); // Generate a random 6-digit number
    
            // Create barcode image using Picqer
            $barcodeGenerator = new \Picqer\Barcode\BarcodeGeneratorPNG();
            $barcodeImage = $barcodeGenerator->getBarcode($barcodeNumber, $barcodeGenerator::TYPE_CODE_128);
    
            // Create and save the barcode entry
            ProductBarcode::create([
                'product_sku' => $product->SKU,
                'barcode_number' => $barcodeNumber,
                'is_used' => 0, // Default to not used
                'received_product_id' => null, // Default to null
                'batch_number' => $batchNumber,
                'barcode_image' => $barcodeImage, // Save the barcode image
            ]);
        }
    
        // Update the product to include the batch number
        $product->batches = json_encode([['Batch_number' => $batchNumber]]);
        $product->Batch_number = $batchNumber; // Add this line
        $product->save();
    
        return redirect()->route('products.index')->with('success', 'Product added successfully!');
    }    
    
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Delete all barcodes associated with this product's SKU
        $product->productBarcodes()->delete();
    
        // Delete all batches associated with this product's SKU
        $product->batches()->delete();
    
        // Delete all received products associated with this product's SKU
        $product->receivedProducts()->delete();
    
        // Delete the product
        $product->delete();
    
        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    }         
}
