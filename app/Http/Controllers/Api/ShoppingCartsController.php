<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ShoppingCartRequest;
use App\Models\ShoppingCart;
use App\Transformers\ShoppingCartTransformer;
use Illuminate\Http\Request;

class ShoppingCartsController extends Controller
{
    public function index(Request $request)
    {
        $query = ShoppingCart::query();

        if ($request->q) {
            $query->where(function ($query) use ($request) {
                $query->whereHas('goods', function ($query) use ($request) {
                    $query->where('title', 'like', "%{$request->q}%");
                });
                $query->OrWhereHas('specification', function ($query) use ($request) {
                    $query->where('title', 'like', "%{$request->q}%");
                });
            });
        }

        $list = $query->isMine()->recent()->get();

        return $this->response->collection($list, new ShoppingCartTransformer());
    }

    public function store(ShoppingCartRequest $shoppingCartRequest)
    {

        $query = ShoppingCart::where('user_id', $this->user->id)
            ->where('goods_id', $shoppingCartRequest->goods_id)
            ->where('goods_specification_id', $shoppingCartRequest->goods_specification_id);

        $shoppingCart = $query->first();
        if (empty($shoppingCart)) {
            $shoppingCart = new ShoppingCart();
            $shoppingCart = $shoppingCart->fill($shoppingCartRequest->all());
            $shoppingCart->user_id = $this->user->id;
        } else {
            $shoppingCart->quantity = $shoppingCart->quantity + $shoppingCartRequest->quantity;
        }

        if (\Auth::user()->agency_id) {
            $shoppingCart->inviter_id = \Auth::user()->id;
        } else {
            $shoppingCart->inviter_id = intval($shoppingCartRequest->inviter_id);
        }

        try {
            $shoppingCart->save();
            return $this->response->created();
        } catch (\Exception $exception) {
            return $this->response->errorBadRequest($exception->getMessage());
        }
    }

    public function update(ShoppingCartRequest $shoppingCartRequest, ShoppingCart $shoppingCart)
    {
        $this->authorize('is-mine', $shoppingCart);
        $shoppingCart->quantity = $shoppingCartRequest->quantity;
        try {
            $shoppingCart->save();
            return $this->response->item($shoppingCart, new ShoppingCartTransformer());
        } catch (\Exception $exception) {
            return $this->response->errorBadRequest($exception->getMessage());
        }
    }

    public function destroy(ShoppingCart $shoppingCart)
    {
        $this->authorize('is-mine', $shoppingCart);
        try {
            $shoppingCart->delete();;
            return $this->response->noContent();
        } catch (\Exception $exception) {
            return $this->response->errorBadRequest($exception->getMessage());
        }
    }

    public function clearAll()
    {
        ShoppingCart::isMine()->delete();
        return $this->response->noContent();
    }
}
