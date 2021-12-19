@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="h2">Редактирование категории атрибута</div>
        <form class="row" action="{{ route('admin.attributes_categories.update', [$model, $attributesCategory]) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="col-8">
                @include('admin.attributes_categories.parts._form')
                <button class="btn btn-success">Сохранить</button>
                <a href="{{ route('admin.hotels.index') }}" class="btn btn-warning">Отмена</a>
            </div>
            <div class="col-4">
            </div>
        </form>
    </div>
@stop
