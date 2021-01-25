<?php

namespace App\Http\Controllers\Receipt;

use App\Http\Controllers\Controller;
use App\Models\PurchaseDetail;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AddReceiptController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        // バリデーション処理
        $request->validate([
            'storeName' => 'required|string',
            'phoneNumber' => 'required|string',
            'purchaseDate' => 'required|date_format:Y年m月d日',
            'records' => 'required',
            'records.*.value' => 'required|integer',
            'records.*.name' => 'required|string',
            'records.*.categoryId' => 'required|integer'
        ]);

        $shop = Shop::query()
            ->where('name', $request->storeName)
            ->first();

        if (!$shop) {
            //TODO 番号検索
            return;
        }

        foreach ($request->records as $record) {
            // モデルに各項目を埋める
            $purchase_detail = new PurchaseDetail();

            $purchase_detail->fill([
                //TODO 仮置き
                'shop_id' => $shop->id,
                'price' => $record["value"],
                'date' => Carbon::createFromFormat(
                    'Y年m月d日',
                    $request->purchaseDate
                ),
                'product_id' => 1, //TODO 正規化商品名(name)とカテゴリから商品idを取得する処理、なければ作成
            ]);

            // DB に追加
            $purchase_detail->save();
        }

        // 成功レスポンスを返す
        return response()->json(["message" => 'success']);
    }
}
