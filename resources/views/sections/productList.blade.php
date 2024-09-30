<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Explore our wide range of products including categories, pricing, and potential profits.">
    <meta name="keywords" content="products, pricing, potential profit, categories">
    <meta name="author" content="Your Name">
</head>
<body>
    <div class="product-section-container">
        <h1 class="product-section-heading">Product List</h1>

        <div class="product-section-row mt-4">
            @foreach($products as $bundle => $items)
                <article class="product-section-card">
                    <img 
                        src="data:image/jpeg;base64,{{ base64_encode($items->first()->Image) }}" 
                        class="product-section-card-img" 
                        alt="Image of {{ $bundle }} product bundle"
                    >
                    <div class="product-section-card-body">
                        <h2 class="product-section-card-title">{{ $bundle }}</h2>
                        
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
                                        <td>{{ $item->maxSRP }}</td>
                                        <td>{{ $item->PotentialProfit }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>                
                </article>
            @endforeach
        </div>
    </div>
</body>
</html>
