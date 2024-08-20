<?php

namespace App\Services;

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
        $minSupport = 0.5; // Thay đổi theo yêu cầu của bạn
        $minConfidence = 0.5; // Thay đổi theo yêu cầu của bạn
        $this->apriori = new Apriori($minSupport, $minConfidence);
    }

    public function getTransactions()
    {
        // // Lấy dữ liệu từ cơ sở dữ liệu
        // $userActions = UserAction::select('user_id', 'product_id')
        //     ->whereIn('action', ['view', 'search', 'purchase'])
        //     ->groupBy('user_id', 'product_id')
        //     ->get();

        // $transactions = $userActions->groupBy('user_id')->map(function ($group) {
        //     return $group->pluck('product_id')->toArray();
        // })->values()->toArray();

        $transactions = UserAction::select('product_id')->where('user_id', 1)->get();
        dd($transactions->toArray());
        $samples = [];
        foreach ($transactions as $transaction) {
            $samples[] = [$transaction->product_id];
        }

        return $samples;
    }


    public function runApriori()
    {
        $transactions = $this->getTransactions();

        // dd($transactions);
        $formattedTransactions = array_map(function ($transaction) {
            return array_map('strval', $transaction);
        }, $transactions);

        // dd($formattedTransactions);


        $labels = [];

        // $samples = [
        //     ["95", "34", "85", "41", "34", "23", "26", "2", "70", "82", "64", "45", "76", "38", "81", "36", "3", "15", "82", "80", "23", "43", "3", "36", "3", "94", "39"],
        //     ["20", "24", "36", "60", "30", "85", "72", "47", "40", "37", "30", "19", "19", "46", "56", "85", "55", "63", "41", "23", "92", "47", "43", "29", "30", "24", "27", "94", "36", "56", "37", "68", "90", "33", "55", "61", "16", "47", "77"],
        //     ["95", "34",  "26", "2", "70", "82", "64", "45", "76", "38", "81", "36", "3", "15", "82", "80", "23", "43", "3", "36", "3", "94", "39"],
        //     ["32", "96", "27", "94", "36", "56", "37", "68", "90", "33", "55", "61", "16", "47", "77"],
        //     ["44", "48", "29", "68", "79", "17", "68", "52", "80", "86", "74", "89", "32", "84", "37", "59", "22", "11",  "44", "54", "85", "41", "34", "23", "26", "2", "70", "82", "64", "45", "76", "38", "81", "36", "3", "15", "82", "80", "23", "43", "3", "36", "3", "94", "39"],
        //     ["32", "96", "35",  "56", "85", "55", "63", "41", "23", "92", "47", "43", "29", "30", "24", "27", "94", "36", "56", "37", "68", "90", "33", "55", "61", "16", "47", "77"],
        //     ["77", "46", "60", "31", "72", "44", "45", "15", "56", "43", "24", "96", "44", "8", "62", "30", "9", "85", "55", "63", "41", "23", "92", "47", "43", "29", "30", "24", "27", "94", "36", "56", "37", "68", "90", "33", "55", "61", "16", "47", "77"],
        //     ["32", "96", "35", "23",  "46", "56", "85", "55", "63", "41", "23", "92", "47", "43", "29", "30", "24", "27", "94", "36", "56", "37", "68", "90", "33", "55", "61", "16", "47", "77"],
        //     ["25", "40", "21", "43", "29", "30", "24", "27", "94", "36", "56", "37", "68", "90", "33", "55", "61", "16", "47", "77"],
        // ];

        // dd($samples);



        $this->apriori->train($formattedTransactions, $labels);

        // $results = $this->apriori->predict(['465']);
        // dd($results);
        // Lấy các quy tắc
        $rules = $this->apriori->getRules();

        // Kiểm tra quy tắc
        dd($rules);

        return $rules;
    }
}
