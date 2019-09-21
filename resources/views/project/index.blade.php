@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card mb-4">
          <div class="card-header">Проекты</div>
          <div class="card-body">
            <ul class="list-group mb-4">
              @if (!$projects->isEmpty())
                @foreach ($projects as $project)
                  <a href="{{route('project.edit', $project->id)}}" class="list-group-item list-group-item-action d-flex justify-content-between">
                      {{$project->name}}
                      <form method="POST" action="{{route('project.destroy', $project->id)}}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Удалить</button>
                      </form>
                  </a>
                @endforeach
              @else
                <span>Ничего нет</span>
              @endif
            </ul>
            <a href="{{route('project.create')}}" class="btn btn-primary">Добавить проект</a>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection