<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;  // Import Log facade for logging
use App\Models\Sale;
use App\Models\Store;
use App\Models\Product;
use App\Models\Billing;
use App\Models\User;  // Import User model
use Carbon\Carbon;

class CreateBillings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:create';  // Define the command name for calling in the terminal

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a single billing record for user ID 43, based on stores and sales';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Get all users (this will fetch all active users, or you can apply other conditions as necessary)
        $users = User::all();  
    
        if ($users->isEmpty()) {
            $this->error('No users found!');
            Log::error('No users found!');
            return;
        }
    
        // Loop through each user
        foreach ($users as $user) {
            $this->info("Processing billings for user: {$user->fname} {$user->lname} (ID: {$user->id})");
            Log::info("Processing billings for user: {$user->fname} {$user->lname} (ID: {$user->id})");
    
            // Get all stores owned by the current user
            $stores = Store::where('store_owner', $user->id)->get();
            $this->info("Total stores found for user {$user->fname} {$user->lname}: " . $stores->count());
            Log::info("User {$user->id} owns {$stores->count()} store(s)");
    
            $totalQuantity = 0;
            $billingBreakdown = [];
    
            // Iterate over each store owned by the user
            foreach ($stores as $store) {
                $this->info("Processing store: {$store->store_name} (ID: {$store->id}) for user {$user->fname} {$user->lname}");
                Log::info("Processing store: {$store->store_name} (ID: {$store->id}) for user {$user->fname} {$user->lname}");
    
                // Get all sales for the current store
                // $billingDate = Carbon::now();

                // Calculate the start date (3 days ago)
                $startDate = Carbon::now()->subDays(4); // Subtract 4 days from the current date
                $endDate = Carbon::now()->subDays(1);   // Subtract 1 day from the current date
                
                // Log the date range for clarity
                $this->info("Sales date range for store {$store->store_name}: Start Date = {$startDate->toDateString()} End Date = {$endDate->toDateString()}");
                Log::info("Sales date range for store {$store->store_name}: Start Date = {$startDate->toDateString()} End Date = {$endDate->toDateString()}");
                
                // Now fetch the sales within this date range
                $sales = Sale::where('sale_made', $store->id)
                    ->where('createdAt', '>=', $startDate)  // Start date (3 days ago)
                    ->where('createdAt', '<=', $endDate)  // End date (current date)
                    ->get();
                
                $this->info("Total sales found for store {$store->store_name}: " . $sales->count());
                Log::info("Total sales found for store {$store->store_name}: " . $sales->count());
    
                // Aggregate quantities by product SKU
                foreach ($sales as $sale) {
                    $this->info("Processing sale ID: {$sale->id} for store {$store->store_name}");
                    Log::info("Processing sale ID: {$sale->id} for store {$store->store_name}");
    
                    $orderedItems = json_decode($sale->ordered_items, true);  // Decode the JSON data
    
                    // Aggregate quantities for each unique SKU
                    foreach ($orderedItems as $item) {
                        $this->info("Processing ordered item SKU: {$item['product_sku']} with quantity: {$item['quantity']}");
                        Log::info("Processing ordered item SKU: {$item['product_sku']} with quantity: {$item['quantity']}");
    
                        // Find the product corresponding to the SKU
                        $product = Product::where('SKU', $item['product_sku'])->first();
                        if ($product) {
                            $subTotal = $item['quantity'] * $product->Consign;  // Calculate sub_total
    
                            // If this SKU already exists in the breakdown, update its quantity and sub_total
                            if (isset($billingBreakdown[$item['product_sku']])) {
                                $billingBreakdown[$item['product_sku']]['quantity'] += $item['quantity'];
                                $billingBreakdown[$item['product_sku']]['sub_total'] += $subTotal;  // Add to the existing sub_total
                            } else {
                                // Otherwise, add a new entry for this SKU with sub_total
                                $billingBreakdown[$item['product_sku']] = [
                                    'product_sku' => $item['product_sku'],
                                    'product_bundle_id' => $item['product_bundle_id'],
                                    'quantity' => $item['quantity'],
                                    'sub_total' => $subTotal,  // Store the sub_total for this SKU
                                ];
                            }
    
                            // Add the quantity to the total quantity
                            $totalQuantity += $item['quantity'];
                        } else {
                            // Log a warning if the product is not found
                            $this->error("Product not found for SKU: {$item['product_sku']}");
                            Log::warning("Product not found for SKU: {$item['product_sku']}");
                        }
                    }
                }
            }
    
            // Convert billingBreakdown to an indexed array
            $billingBreakdown = array_values($billingBreakdown);
    
            // Log the total quantity and breakdown before creating the billing
            $this->info("Total quantity for user {$user->fname} {$user->lname}: {$totalQuantity}");
            Log::info("Total quantity for user {$user->fname} {$user->lname}: {$totalQuantity}");
    
            // Calculate the total bill from the sub_totals
            $totalBill = array_sum(array_column($billingBreakdown, 'sub_total'));
    
            $startDate = Carbon::now()->subDays(4); // Subtract 4 days from the current date
            $endDate = Carbon::now()->subDays(1); 
            
            // Format the date range as "Nov. 8, 2024 - Nov. 11, 2024"
            $salesDateRange = $startDate->format('M. d, Y') . ' - ' . $endDate->format('M. d, Y');
            
            // Now include the sales_date_range in the create method
            Billing::create([
                'user_id' => $user->id,
                'total_bill' => $totalBill,  // Use the total bill calculated from sub_totals
                'bill_issued' => Carbon::now(),  // Current date and time
                'billing_breakdown' => json_encode($billingBreakdown),  // Store the breakdown as a JSON string
                'status' => 'To Pay',  // Default status "To Pay"
                'sales_date_range' => $salesDateRange,  // Add the sales_date_range here
            ]);
    
            $this->info("Billing created for user {$user->fname} {$user->lname} with total quantity: {$totalQuantity} and total bill: {$totalBill} and status: To Pay");
            Log::info("Billing created for user {$user->fname} {$user->lname} with total quantity: {$totalQuantity} and total bill: {$totalBill} and status: To Pay");
        }
    }        
}
