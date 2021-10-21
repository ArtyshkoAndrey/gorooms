@extends('layouts.admin')

@section('content')
    <div class="container">
        @if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
        <div class="card">
            <div class="card-header">
                <div class="d-flex w-100 justify-content-between align-items-center">
                    <div class="h4 p-0 m-0">Список отелей</div>
                    <a href="{{ route('admin.hotels.create') }}" class="btn btn-primary btn-sm">Новый отель</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Изображение</th>
                            <th>Наименование</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($hotels AS $hotel)
                            <tr>
                                <td>{{ $hotel->id }}</td>
                                <td>
                                    <img src="{{ asset($hotel->image->path) }}" alt="" class="img-fluid"
                                         style="max-width: 100px">
                                </td>
                                <td>
                                    <a href="{{ route('admin.hotels.show', $hotel) }}" class="">{{ $hotel->name }}</a>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.hotels.edit', $hotel) }}" class="btn btn-success">Изменить</a>
                                        @if($hotel->show)
                                            <a href="{{ route('admin.hotels.block', $hotel) }}" class="btn btn-primary">Заблокировать отель</a>
                                        @else
                                            <a href="{{ route('admin.hotels.unblock', $hotel) }}" class="btn btn-primary">Разблокировать отель</a>
                                        @endif
                                        <button type="button" data-action="{{ route('admin.hotels.destroy', $hotel) }}" class="btn btn-danger js-delete">Удалить</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $hotels->render() }}
            </div>
        </div>
    </div>
@stop
