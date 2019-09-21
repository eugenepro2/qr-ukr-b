@extends('layouts.app')

@section('content')
  <div class="container">
    <form method="POST" action="{{route('package.store')}}">
      @csrf
      <div class="form-group">
        <label for="packageName">Название посылки</label>
        <input type="text" class="form-control" id="packageName" name="name" placeholder="Название посылки">
      </div>
      <div class="form-group">
        <label for="from">С какого проекта?</label>
        <select class="form-control" name="from_project" id="from">
          @foreach ($projects as $project)
            <option value="{{$project->id}}">{{$project->name}}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label for="to">На какой проект?</label>
        <select class="form-control" name="to_project" id="to">
          @foreach ($projects as $project)
            <option value="{{$project->id}}">{{$project->name}}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Создать отправку</button>
    </form>
  </div>
@endsection