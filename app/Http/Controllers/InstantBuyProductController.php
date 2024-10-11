<?php

namespace App\Http\Controllers;

use App\Models\InstantBuyProduct;
use App\Models\Store;
use App\Models\StoreInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InstantBuyProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Require authentication for all methods in this controller
    }

    public function create(Request $request)
    {
        // Get the store_id from the URL
        $storeId = $request->query('store_id');
    
        // Check if store_id is present
        if (!$storeId) {
            return redirect()->route('stores.index')->with('error', 'No store ID specified');
        }
    
        // Find the store by store_id
        $store = Store::where('id', $storeId)
            ->where('store_owner', auth()->user()->id)
            ->first();
    
        if (!$store) {
            return redirect()->route('stores.index')->with('error', 'You don\'t have authority to add product for this store');
        }
    
        // Fetch store inventory filtered by store_id
        $storeInventory = StoreInventory::where('store_id', $storeId)->get();
    
        return view('pages.instantBuyAdd', compact('storeId', 'storeInventory')); // Pass the storeId and inventory to the view
    }  

    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'product_barcode' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'size' => 'nullable|string|max:50',
            'dimension' => 'nullable|string|max:100',
            'price' => 'required|numeric',
            'images.*' => 'nullable|image|max:2048', // Validate each image file
            'issue' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:50',
            'store_id' => 'required|integer',
            'video' => 'nullable|file|mimes:mp4,avi,mov|max:51200', // Validate video file
            'product_sku' => 'required|string|max:255', // Validate product_sku
        ]);
    
        // Initialize an array to hold binary image data
        $imageData = [];
    
        // Loop through each uploaded image
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if (count($imageData) < 6) {
                    $imageData[] = file_get_contents($image->getRealPath());
                }
            }
        }
    
        // Handle video upload (only one video)
        $videoData = null;
        if ($request->hasFile('video')) {
            $videoData = file_get_contents($request->file('video')->getRealPath());
        }
    
        // Create a new InstantBuyProduct with images and video
        InstantBuyProduct::create(array_merge($request->except('images', 'video'), [
            'images' => json_encode($imageData), // Store images as JSON
            'video' => $videoData, // Store single video binary
            'product_sku' => $request->input('product_sku'), // Include product_sku
        ]));
    
        // Redirect to stores.index with success message
        return redirect()->route('stores.index')->with('success', 'Product created successfully.');
    }
         
}


