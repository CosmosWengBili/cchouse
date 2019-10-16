<?php

namespace App\Http\Controllers;

use App\EditorialReview;
use App\Responser\NestedRelationResponser;
use Illuminate\Http\Request;

class EditorialReviewController extends Controller
{
    public function index(Request $request)
    {
        $responseData = new NestedRelationResponser();
        $responseData
            ->index(
                'editorialReviews',
                $this->limitRecords(EditorialReview::with($request->withNested))
            )
            ->relations($request->withNested);

        $data = $responseData->get();
        // 拿掉 original_value, edit_value 只顯示差異化的部分
        $data['data']['editorialReviews'] = collect($data['data']['editorialReviews'])->map(function ($item, $key) {
            return collect($item)->except(['original_value', 'edit_value', 'extra_data']);
        })
            ->toArray();

        return view('editorial_reviews.index', $data);
    }

    public function show(Request $request, EditorialReview $editorialReview)
    {
        $responseData = new NestedRelationResponser();
        $responseData
            ->show($editorialReview->load($request->withNested))
            ->relations($request->withNested);
        $data = $responseData->get();
        unset($data['data']['extra_data']);

        return view('editorial_reviews.show', $data);
    }

    public function pass(Request $request, $id)
    {
        $editorialReview = EditorialReview::find($id);
        $editorialReview->status = '已通過';
        $editorialReview->save();

        return response()->json(true);
    }

    public function notPass(Request $request, $id)
    {
        $editorialReview = EditorialReview::find($id);
        $editorialReview->status = '不通過';
        $editorialReview->save();

        return response()->json(true);
    }
}
