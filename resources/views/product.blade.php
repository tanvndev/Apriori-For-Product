<!DOCTYPE html>
<html>

<head>
    <title>Product Suggestions</title>
</head>

<body>
    <h1>Product Suggestions</h1>
    <ul>
        @foreach ($products as $product)
        <li>{{ $product->name }}</li>
        @endforeach
    </ul>
</body>

</html>
