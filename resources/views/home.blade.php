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
								<a href="{{route('project.edit', $project->id)}}" class="list-group-item list-group-item-action">
										{{$project->name}}
								</a>
							@endforeach
						@else
							<span>Ничего нет</span>
						@endif
					</ul>
					<a href="{{route('project.create')}}" class="btn btn-primary">Добавить проект</a>
				</div>
			</div>

			<div class="card">
				<div class="card-header">Посылки</div>
				<div class="card-body">
					<ul class="list-group mb-4">
						@if (!$packages->isEmpty())
							@foreach ($packages as $package)
								<a href="{{route('package.edit', $package->id)}}" class="list-group-item list-group-item-action">
									Название посылки: {{$package->name}} | Откуда: {{$package->fromProject->name}} | 
									Куда: {{$package->toProject->name}} | Бригадир: {{$package->user->name}}
								</a>
							@endforeach		
						@else
								<span>Ничего нет</span>
						@endif
						
					</ul>
					<a href="{{route('package.create')}}" class="btn btn-primary">Добавить посылку</a>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
