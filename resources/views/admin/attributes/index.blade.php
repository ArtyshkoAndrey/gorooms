@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="d-flex w-100 justify-content-between align-items-center">
                    <div class="h4 p-0 m-0">Список атрибутов для {{ $category === 'room' ? 'номеров' : 'отелей' }}</div>
                    <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary btn-sm">Новый атрибут</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Наименование</th>
                            <th>Описание</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($attributes AS $attribute)
                            <tr>
                                <td>{{ $attribute->id }}</td>
                                <td>{{ $attribute->name }}</td>
                                <td>{{ $attribute->description }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.attributes.edit', $attribute) }}" class="btn btn-success">Изменить</a>
                                        <button type="button" data-action="{{ route('admin.attributes.destroy', $attribute) }}" class="btn btn-danger js-delete">Удалить</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
