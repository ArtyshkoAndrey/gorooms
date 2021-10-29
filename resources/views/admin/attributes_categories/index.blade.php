@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="d-flex w-100 justify-content-between align-items-center">
                    <div class="h4 p-0 m-0">Категории атрибутов</div>
                    <a href="{{ route('admin.attributes_categories.create') }}" class="btn btn-primary btn-sm">Новая категория</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Наименование</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($attribute_categories AS $attribute_category)
                            <tr>
                                <td>{{ $attribute_category->id }}</td>
                                <td>{{ $attribute_category->name }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.attributes_categories.edit', $attribute_category) }}" class="btn btn-success">Изменить</a>
                                        <button type="button" data-action="{{ route('admin.attributes_categories.destroy', $attribute_category) }}" class="btn btn-danger js-delete">Удалить</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $attribute_categories->render() }}
            </div>
        </div>
    </div>
<<<<<<< HEAD
@stop
=======
@stop
>>>>>>> c2cae19... Attributes categories
