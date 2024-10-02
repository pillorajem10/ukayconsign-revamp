<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Thank You, {{ $order->first_name }}!</h1>
    <p>Your order has been placed successfully.</p>
    <p><strong>Order ID:</strong> {{ $order->id }}</p>
    <p><strong>Total Price:</strong> ₱{{ number_format($order->total_price, 2) }}</p>
    
    <h2>Products Ordered:</h2>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach (json_decode($order->products_ordered) as $product)
                <tr>
                    <td>{{ $product->bundle_name }}</td>
                    <td>{{ $product->quantity }}</td>
                    <td>₱{{ number_format($product->price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <p>We appreciate your business!</p>
</body>
</html>
