<?php

namespace App\Http\Controllers;

use App\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        Item::create($request->all());
        return redirect()->route('package.show', $request->packages_id);
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        $item = Item::find($id);
        $packages_id = $item->packages_id;
        $item->delete();
        return redirect()->route('package.show', $packages_id);
    }
}
