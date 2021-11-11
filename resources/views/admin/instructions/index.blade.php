@extends('layouts.admin')

@section('content')
  <div class="container">
    <div class="card">
      <div class="card-header">
        <div class="d-flex w-100 justify-content-between align-items-center">
          <div class="h4 p-0 m-0">Список инструкций</div>
          <a href="{{ route('admin.instructions.create') }}" class="btn btn-primary btn-sm">Добавить рездел инструкции</a>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table">
            <thead>
            <tr>
              <th>#</th>
              <th>Заголовок</th>
              <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($instructions as $instruction)
              <tr>
                <td>{{ $instruction->id }}</td>
                <td>{{ $instruction->header }}</td>
                <td>
                  <div class="btn-group btn-group-sm">
                    <a href="{{ route('admin.instructions.edit', $instruction) }}" class="btn btn-primary">Изменить</a>
                    <button data-action="{{ route('admin.instructions.destroy', $instruction) }}" class="btn btn-danger js-delete">Удалить</button>
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
