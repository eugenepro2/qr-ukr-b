@extends('layouts.app')

@section('content')
  <div class="container">
    <form method="POST" action="{{route('project.update', $project->id)}}">
      @csrf
      @method('PUT')
      <div class="form-group">
        <label for="packageName">Название проекта</label>
        <input type="text" class="form-control" id="packageName" name="name" placeholder="Название проекта" value="{{$project->name}}">
      </div>
      <button type="submit" class="btn btn-primary">Сохранить проект</button>
    </form>
  </div>
@endsection