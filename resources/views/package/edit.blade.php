@extends('layouts.app')

@section('content')
  <div class="container">
    <form method="POST" action="{{route('package.update', $package->id)}}">
      @csrf
      @method('PUT')
      <div class="form-group">
        <label for="packageName">Название посылки</label>
        <input type="text" class="form-control" id="packageName" name="name" 
      placeholder="Название посылки" value="{{$package->name}}">
      </div>
      <div class="form-group">
        <label for="from">С какого проекта?</label>
        <select class="form-control" name="from_project" id="from">
          @foreach ($projects as $project)
          <option {{ ($project->id == $package->from_project) ? 'selected' : '' }} value="{{$project->id}}">{{$project->name}}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label for="to">На какой проект?</label>
        <select class="form-control" name="to_project" id="to">
          @foreach ($projects as $project)
            <option {{ ($project->id == $package->to_project) ? 'selected' : '' }} value="{{$project->id}}">{{$project->name}}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Сохранить</button>
    </form>
  </div>
@endsection