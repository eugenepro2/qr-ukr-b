<?php

namespace App\Http\Controllers;

use App\{Project, Package};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
       return view('package.index');
    }
    public function edit($id)
    {
        return view('package.edit', [
            'projects' => Project::all(),
            'package' => Package::find($id)
        ]);
    }
}
