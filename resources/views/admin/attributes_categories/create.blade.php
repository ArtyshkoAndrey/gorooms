@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="h2">Создание категории атрибута</div>
        <form class="row" action="{{ route('admin.attributes_categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="col-8">
                @include('admin.attributes_categories.parts._form')
                <button class="btn btn-success">Создать</button>
                <a href="{{ route('admin.attributes_categories.index') }}" class="btn btn-warning">Отмена</a>
            </div>
            <div class="col-4">
            </div>
        </form>
    </div>
<<<<<<< HEAD
@stop
=======
@stop
>>>>>>> c2cae19... Attributes categories
