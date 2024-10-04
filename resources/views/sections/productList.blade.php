<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Explore our wide range of products including categories, pricing, and potential profits.">
    <meta name="keywords" content="products, pricing, potential profit, categories">
    <meta name="author" content="Ukay Supplier">
</head>
<body class="loading">
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="product-section-container">
        <div class="product-section-row mt-4">
            @foreach($products as $bundle => $items)
                <div class="product-section-card">
                    <div class="product-section-card-image">
                        <img src="data:image/jpeg;base64,{{ base64_encode($items->first()->Image) }}" 
                            class="product-section-card-img" 
                            alt="Image of {{ $bundle }} product bundle">
                    </div>
                    <div class="product-section-card-body">
                        <h5 class="product-section-card-title">{{ $bundle }}</h5>
                        
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
                                @foreach($items as $item)
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
                
                        <div class="button-container">
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
                        </div>
                    </div>
                </div>                   
            @endforeach                              
        </div>
    </div>
    
    <script src="{{ asset('js/home.js?v=1.9') }}"></script>
</body>
</html>
