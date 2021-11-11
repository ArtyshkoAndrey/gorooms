@extends('layouts.admin')

@section('content')
  <div class="container">
    <div class="h2">Редактирование инструкции</div>
    <form class="row" action="{{ route('admin.instructions.update', $instruction) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="col-8">
        @include('admin.instructions._form')
        <button class="btn btn-success">Сохранить</button>
        <a href="{{ route('admin.moderators.index') }}" class="btn btn-warning">Отмена</a>
      </div>
      <div class="col-4">
      </div>
    </form>
  </div>
@stop

@section('js')
  <script>
    $( document ).ready(function() {
      tinymce.init({
        selector: 'textarea',
        language: 'ru',
        plugins: 'advlist autolink lists link image imagetools media charmap print preview hr anchor pagebreak',
        toolbar_mode: 'floating',
        image_title: true,
        automatic_uploads: true,
        file_picker_types: 'image',
        file_picker_callback: function (cb, value, meta) {
          let input = document.createElement('input');
          input.setAttribute('type', 'file');
          input.setAttribute('accept', 'image/*');

          /*
            Note: In modern browsers input[type="file"] is functional without
            even adding it to the DOM, but that might not be the case in some older
            or quirky browsers like IE, so you might want to add it to the DOM
            just in case, and visually hide it. And do not forget do remove it
            once you do not need it anymore.
          */

          input.onchange = function () {
            let file = this.files[0];

            let reader = new FileReader();
            reader.onload = function () {
              /*
                Note: Now we need to register the blob in TinyMCEs image blob
                registry. In the next release this part hopefully won't be
                necessary, as we are looking to handle it internally.
              */
              let id = 'blobid' + (new Date()).getTime();
              let blobCache =  tinymce.activeEditor.editorUpload.blobCache;
              let base64 = reader.result.split(',')[1];
              let blobInfo = blobCache.create(id, file, base64);
              blobCache.add(blobInfo);

              /* call the callback and populate the Title field with the file name */
              cb(blobInfo.blobUri(), { title: file.name });
            };
            reader.readAsDataURL(file);
          };

          input.click();
        },
      });
    });
  </script>
@endsection
