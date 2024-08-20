<?php

namespace App\Services;

use App\Models\Product;
use App\Models\UserAction;
use App\Services\Interfaces\AprioriServiceInterface;
use Phpml\Association\Apriori;
use Phpml\Dataset\ArrayDataset;

class AprioriService implements AprioriServiceInterface
{
    protected $apriori;

    public function __construct()
    {
        // Khởi tạo đối tượng Apriori với minSupport và minConfidence
        $minSupport = 0.1; // Thay đổi theo yêu cầu của bạn
        $minConfidence = 0.5; // Thay đổi theo yêu cầu của bạn
        $this->apriori = new Apriori($minSupport, $minConfidence);
    }

    public function getTransactions(): array
    {
        // // Lấy dữ liệu từ cơ sở dữ liệu
        // $userActions = UserAction::select('user_id', 'product_id')
        //     ->whereIn('action', ['view', 'search', 'purchase'])
        //     ->groupBy('user_id', 'product_id')
        //     ->get();

        // $transactions = $userActions->groupBy('user_id')->map(function ($group) {
        //     return $group->pluck('product_id')->toArray();
        // })->values()->toArray();

        // $behaviors = UserBehavior::all()->groupBy('user_id');
        $transactions = UserAction::all()->take(50000)->groupBy('user_id')->map(function ($group) {
            return $group->pluck('product_id')->unique()->values()->toArray();
        });

        return $transactions->toArray();
    }

    public function getRulesFromJson()
    {
        $path = storage_path('app/apriori_rules.json');

        if (!file_exists($path)) {
            return [];
        }

        $jsonContent = file_get_contents($path);

        $rules = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Failed to decode JSON: " . json_last_error_msg());
        }

        return $rules;
    }


    public function runApriori(): void
    {
        $transactions = $this->getTransactions();

        // dd($transactions);
        $formattedTransactions = array_map(function ($transaction) {
            return array_map('strval', $transaction);
        }, $transactions);

        // dd($formattedTransactions);
        $labels = [];
        $this->apriori->train($formattedTransactions, $labels);

        // $results = $this->apriori->predict(['465']);
        // dd($results);
        // Lấy các quy tắc
        // $rules = $this->apriori->getRules();
        // Kiểm tra quy tắc
        // dd($rules);
        // return $rules;
    }

    // public function getRecommendations(array $products): array
    // {
    //     // Get product recommendations based on user's current products
    //     return $this->apriori->predict($products);
    // }

    public function getRecommendations(array $products): array
    {
        $rules = $this->getRulesFromJson();
        // dd($rules);

        // dd($products);
        $recommendedProducts = [];

        foreach ($rules as $rule) {
            $antecedents = $rule['antecedents'];

            // Kiểm tra nếu sản phẩm hiện tại có trong antecedents
            if (array_intersect($products, $antecedents)) {
                // dd(1);
                $consequents = $rule['consequents'];

                // Tìm sản phẩm từ database dựa trên consequents
                $productsFromDB = Product::whereIn('id', $consequents)->get();
                $recommendedProducts = array_merge($recommendedProducts, $productsFromDB->toArray());
            }
        }


        return $recommendedProducts;
    }
}
