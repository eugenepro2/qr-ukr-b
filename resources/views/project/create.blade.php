@extends('layouts.app')

@section('content')
  <div class="container">
    <form method="POST" action="{{route('project.store')}}">
      @csrf
      <div class="form-group">
        <label for="packageName">Название проекта</label>
        <input type="text" class="form-control" id="packageName" name="name" placeholder="Название проекта">
      </div>
      <button type="submit" class="btn btn-primary">Добавить проект</button>
    </form>
  </div>
@endsection