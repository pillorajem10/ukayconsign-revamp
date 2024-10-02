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
        <div class="product-section-row mt-4">
            @foreach($products as $bundle => $items)
                <article class="product-section-card">
                    <img src="data:image/jpeg;base64,{{ base64_encode($items->first()->Image) }}" 
                        class="product-section-card-img" 
                        alt="Image of {{ $bundle }} product bundle">
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
                                    <td>{{ $item->maxSRP }}</td>
                                    <td>{{ $item->PotentialProfit }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                
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
                </article>
            @endforeach                              
        </div>

        <!-- CUSTOM PAGINATION -->
        <div class="product-section-pagination mt-4">
            @if ($products->hasPages())
                <ul class="product-section-pagination-list">
                    {{-- Previous Page Link --}}
                    @if ($products->onFirstPage())
                        <li class="product-section-disabled"><span>Prev</span></li>
                    @else
                        <li>
                            <a class="product-section-link" 
                            href="{{ $products->previousPageUrl() }}{{ $search ? '&search=' . $search : '' }}">Prev</a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @php
                        $currentPage = $products->currentPage();
                        $lastPage = $products->lastPage();
                    @endphp

                    {{-- Show first page --}}
                    <li>
                        <a class="product-section-link" href="{{ $products->url(1) }}{{ $search ? '&search=' . $search : '' }}">1</a>
                    </li>

                    {{-- Show ellipsis if needed --}}
                    @if ($currentPage > 3)
                        <li class="product-section-ellipsis"><span>...</span></li>
                    @endif

                    {{-- Show pages around the current page --}}
                    @for ($i = max(2, $currentPage - 1); $i <= min($lastPage - 1, $currentPage + 1); $i++)
                        <li class="{{ $i == $currentPage ? 'product-section-active' : '' }}">
                            <a class="product-section-link" href="{{ $products->url($i) }}{{ $search ? '&search=' . $search : '' }}">{{ $i }}</a>
                        </li>
                    @endfor

                    {{-- Show ellipsis if needed --}}
                    @if ($currentPage < $lastPage - 2)
                        <li class="product-section-ellipsis"><span>...</span></li>
                    @endif

                    {{-- Show last page --}}
                    @if ($lastPage > 1)
                        <li>
                            <a class="product-section-link" href="{{ $products->url($lastPage) }}{{ $search ? '&search=' . $search : '' }}">{{ $lastPage }}</a>
                        </li>
                    @endif

                    {{-- Next Page Link --}}
                    @if ($products->hasMorePages())
                        <li>
                            <a class="product-section-link" 
                            href="{{ $products->nextPageUrl() }}{{ $search ? '&search=' . $search : '' }}">Next</a>
                        </li>
                    @else
                        <li class="product-section-disabled"><span>Next</span></li>
                    @endif
                </ul>
            @endif
        </div>
    </div>
</body>
</html>
