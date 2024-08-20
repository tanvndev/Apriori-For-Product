<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Interfaces\AprioriServiceInterface;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $aprioriService;

    public function __construct(AprioriServiceInterface $aprioriService)
    {
        $this->aprioriService = $aprioriService;
    }

    public function index()
    {
        $products = Product::query()->paginate(10);
        return view('products.index', compact('products'));
    }

    public function suggestProducts(Request $request)
    {
        $this->aprioriService->runApriori();
        $result = $this->aprioriService->getRecommendations([7]);

        dd($result);
        // Xử lý kết quả để tìm sản phẩm liên quan đến sản phẩm $productId
        // Giả sử bạn đã có cách để lọc sản phẩm liên quan từ kết quả Apriori

        // return view('products.suggestions', ['suggestions' => $suggestions]);
    }

    public function trainApriori()
    {
        // Train mô hình với dữ liệu giao dịch
        $this->aprioriService->runApriori();

        return response()->json(['message' => 'Apriori model trained successfully']);
    }
}
