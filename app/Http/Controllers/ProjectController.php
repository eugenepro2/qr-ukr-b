<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        return view('project.index', [
            'projects' => Project::all()
        ]);
    }
    public function create()
    {
        return view('project.create');
    }
    public function store(Request $request)
    {
        Project::create($request->all());
        return redirect()->route('project.index');
    }
    public function edit($id)
    {
        return view('project.edit', [
            'project' => Project::find($id)
        ]);
    }
    public function update(Request $request, $id)
    {
        Project::find($id)->update($request->all());
        return redirect()->route('project.index');
    }
    public function destroy($id)
    {
        Project::find($id)->delete();
        return redirect()->route('project.index');
    }
}
