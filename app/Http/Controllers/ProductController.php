<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\AprioriServiceInterface;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $aprioriService;

    public function __construct(AprioriServiceInterface $aprioriService)
    {
        $this->aprioriService = $aprioriService;
    }

    public function suggestProducts(Request $request)
    {
        $result = $this->aprioriService->runApriori();

        dd($result);
        // Xử lý kết quả để tìm sản phẩm liên quan đến sản phẩm $productId
        // Giả sử bạn đã có cách để lọc sản phẩm liên quan từ kết quả Apriori

        // return view('products.suggestions', ['suggestions' => $suggestions]);
    }
}
