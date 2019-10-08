<?php

namespace App\Http\Controllers;

use App\EditorialReview;
use Illuminate\Http\Request;

class EditorialReviewController extends Controller
{
    public function pass(Request $request, EditorialReview $editorialReview)
    {
        $editorialReview->status = '已通過';
        $editorialReview->save();

        return redirect($request->_redirect);
    }
}
