<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2019/8/18
 * Time: 00:48
 */

namespace App\Http\Controllers\Api;

use App\Models\Address;
use App\Rules\CnMobile;
use App\Transformers\AddressTransformer;
use Illuminate\Http\Request;

class AddressesController extends Controller
{
    public function index()
    {
        return $this->response->collection($this->user->addresses, new AddressTransformer());
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:20',
            'mobile'  => ['required', new CnMobile()],
            'province_code' => 'required',
            'city_code' => 'required',
            'district_code' => 'required',
            'address' => 'required|max:255',
            'is_default' => ['required', 'in:0,1']
        ], [], [
            'name' => '收件人',
            'mobile'  => '手机号码',
            'province_code' => '省',
            'city_code' => '市',
            'district_code' => '区',
            'address' => '详细地址',
            'is_default' => '默认地址',
        ]);

        $address = $this->user->addresses()->create($request->only(['name', 'mobile', 'province_code', 'city_code', 'district_code', 'address', 'is_default']));
        if ($address->is_default) {
            $this->user->addresses()->where('id', '<>', $address->id)->update(['is_default' => 0]);
        }

        return $this->response->item($address, new AddressTransformer());
    }

    public function update(Address $address, Request $request)
    {
        $this->authorize('is-mine', $address);

        $this->validate($request, [
            'name' => 'required|max:20',
            'mobile'  => ['required', new CnMobile()],
            'province_code' => 'required',
            'city_code' => 'required',
            'district_code' => 'required',
            'address' => 'required|max:255',
            'is_default' => ['required', 'in:0,1']
        ], [], [
            'name' => '收件人',
            'mobile'  => '手机号码',
            'province_code' => '省',
            'city_code' => '市',
            'district_code' => '区',
            'address' => '详细地址',
            'is_default' => '默认地址',
        ]);

        $address->fill($request->only(['name', 'mobile', 'province_code', 'city_code', 'district_code', 'address', 'is_default']));

        $address->save();
        return $this->response->item($address, new AddressTransformer());
    }

    public function destroy(Address $address)
    {
        $this->authorize('is-mine', $address);

        $address->delete();
        return $this->response->noContent();
    }

    public function show(Address $address)
    {
        $this->authorize('is-mine', $address);

        return $this->response->item($address, new AddressTransformer());
    }

    public function getDefault()
    {
        $address = $this->user->addresses()->where('is_default', 1)->first();
        if ($address) return $this->response->item($address, new AddressTransformer());
        else return $this->response->noContent();
    }
}