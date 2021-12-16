@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="h2">Редактирование атрибута</div>
        <form class="row" action="{{ route('admin.attributes.update', [$model, $attribute]) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="col-8">
                @include('admin.attributes.parts._form')
                <button class="btn btn-success">Сохранить</button>
                <a href="{{ route('admin.hotels.index', $model) }}" class="btn btn-warning">Отмена</a>
            </div>
            <div class="col-4">
            </div>
        </form>
    </div>
@stop
