<?php

namespace App\Http\Controllers;

use App\{Item, Project, Package};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PackageController extends Controller
{
    public function index()
    {
        return view('package.index', [
            'packages' => Package::all()
        ]);
    }
    public function create()
    {
        return view('package.create', [
            'projects' => Project::all()
        ]);
    }
    public function store(Request $request)
    {
       $package = Package::create($request->all());
       $package->user_id = Auth::user()->id;
       $package->save(); 
       Storage::disk('public')->put("{$package->id}-qr.svg", \QrCode::size(500)->generate(env('APP_URL') . "/package/{$package->id}"));
       return redirect()->route('package.show', $package->id);
    }
    public function show($id)
    {
        $package = Package::find($id);
        return view('package.show', [
            'package' => $package,
            'items' => Item::where('packages_id', $id)->get(),
            'qr' => Storage::disk('public')->url("{$id}-qr.svg")
        ]);
    }
    public function edit($id)
    {
        return view('package.edit', [
            'projects' => Project::all(),
            'package' => Package::find($id)
        ]);
    }
    public function update(Request $request, $id)
    {
        Package::find($id)->update($request->all());
        return redirect()->route('package.show', $id);
    }
    public function destroy($id)
    {
        Package::find($id)->delete();
        return redirect()->route('project.index');
    }
}
