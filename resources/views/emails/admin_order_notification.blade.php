<!DOCTYPE html>
<html>
<head>
    <title>New Order Notification</title>
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
    <h1>New Order Received!</h1>
    <p>A new order has been placed by {{ $order->first_name }} {{ $order->last_name }}.</p>
    <p><strong>Order ID:</strong> {{ $order->id }}</p>
    <p><strong>Customer Email:</strong> {{ $order->email }}</p>
    <p><strong>Total Price:</strong> ₱{{ number_format($order->total_price, 2) }}</p>
    <p><strong>Delivery Address:</strong> {{ $order->address }}</p>
    <p><strong>Store Name:</strong> {{ $order->store_name }}</p>

    <h2>Order Details:</h2>
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

    <p>Thank you for your attention!</p>
</body>
</html>
