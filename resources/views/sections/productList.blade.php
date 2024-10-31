<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Explore our wide range of products including categories, pricing, and potential profits.">
    <meta name="keywords" content="products, pricing, potential profit, categories">
    <meta name="author" content="Ukay Supplier">
    <link rel="stylesheet" href="{{ asset('css/homePage.css?v=4.8') }}">
</head>
<body class="loading">
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="product-section-container">
        <div class="product-section-row">
            @foreach($products as $bundle => $items)
                @php
                    // Sort the items based on category
                    $sortedItems = $items->sortBy(function($item) {
                        $order = ['Essential', 'Signature', 'Exclusive'];
                        return array_search($item->Category, $order);
                    });
                
                    // Log details images for each sorted item in the bundle
                    foreach ($sortedItems as $item) {
                        $detailsImages = json_decode($item->details_images) ?? []; // Default to an empty array if null
                        \Log::info('Details Images for SKU: ' . $item->SKU, ['details_images' => $detailsImages]);
                    }
                @endphp
        
                <div class="product-section-card">
                    <div class="product-section-card-image">
                            <img src="data:image/jpeg;base64,{{ base64_encode($items->first()->Image) }}" 
                                class="product-section-card-img open-modal" 
                                alt="Image of {{ $items->first()->Bundle }} product bundle"
                                data-details-images='{{ json_encode(json_decode($items->first()->details_images) ?? []) }}' 
                                data-bundle="{{ $items->first()->Bundle }}">
                    </div>                    
                    <div class="product-section-card-body">
                        <div><h5 class="product-section-card-title">{{ $bundle }}</h5></div>
                
                        <div class="table-container"> <!-- Added a container for flex alignment -->
                            <table class="product-section-table">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>QTY</th>
                                        <th>Consign Price/Pc</th>
                                        <th>SRP</th>
                                        <th>Potential Profit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sortedItems as $item)
                                    <tr>
                                        <td>{{ $item->Category }}</td>
                                        <td>{{ $item->Bundle_Qty }}</td>
                                        <td>{{ $item->Consign }}</td>
                                        <td>{{ $item->SRP }}</td>
                                        <td>{{ $item->PotentialProfit }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="button-container"> <!-- Moved button container here -->
                                <form class="add-to-cart-form" method="POST" action="{{ route('cart.add') }}">
                                    @csrf
                                    <input type="hidden" name="bundle_name" value="{{ $bundle }}">
                                    @foreach($items as $item)
                                        <input type="hidden" name="products[{{ $item->SKU }}][quantity]" value="{{ $item->Bundle_Qty }}">
                                        <input type="hidden" name="products[{{ $item->SKU }}][price]" value="{{ $item->Consign }}">
                                        <input type="hidden" name="products[{{ $item->SKU }}][price_type]" value="SRP">
                                    @endforeach
                                    <button type="submit" class="product-section-add-to-cart-btn">Add Bundle to Cart</button>
                                </form>
                                
                                <!-- Add Show Bundle Samples Button -->
                                <button type="button" class="product-section-show-samples-btn" 
                                        onclick="openImageModal(this)" 
                                        data-details-images='{{ json_encode(json_decode($items->first()->details_images) ?? []) }}'
                                        data-bundle="{{ $items->first()->Bundle }}">
                                    View Product
                                </button>
                            </div>                            
                        </div>
                    </div>
                </div>    
            @endforeach                              
        </div>
    </div>

    <!-- Modal Structure -->
    <div id="imageModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h2 class="modal-header">Bundle Product Samples For: </h2> <!-- Header updated dynamically -->
            <div class="modal-images" id="modalImagesContainer"></div>
        </div>
    </div>

    

    <script src="{{ asset('js/home.js?v=4.8') }}"></script>
</body>
</html>
