@extends('lk.layouts.app')

@section('content')

  <section class="gray part">
    <div class="container">
      <div class="row part__top">
        <div class="col-12">
          <h2 class="title">Объект</h2>
        </div>
      </div>
      <div class="row part__middle">
        <div class="col-12">
          <p class="heading">Название объекта</p>
        </div>
      </div>
      <div class="row">
        <div class="col-12">
          <p class="heading">Тип Объекта размещения</p>
        </div>
      </div>
      <div class="row part__content">
        <div class="col-4">
          <input type="phone" class="field" placeholder="Телефон 1 объекта">
        </div>
        <div class="col-4">
          <input type="phone" class="field" placeholder="Телефон 2 объекта">
        </div>
        <div class="col-4">
          <input type="email" class="field" placeholder="Email объекта для бронирований">
        </div>
      </div>
      <div class="row part__bottom">
        <div class="col-12">
          <button class="button save-button" id="save1">Сохранить</button>

        </div>
      </div>
    </div>
  </section>

@endsection

@section('js')
  <script>
    $("input[type='phone']").mask("+7 (999) 999 99-99");

    $(document).ready(function(){
      $('.sortable').sortable({
        items: '.dz-image-preview',
      });

    });

    //DropzoneJS snippet - js
    Dropzone.autoDiscover = false;
    // instantiate the uploader
    $('#file-dropzone').dropzone({
      url: "/file/post",
      maxFiles: 6,
      thumbnailWidth: 360,
      thumbnailHeight: 260,
      addRemoveLinks: true,
      previewsContainer: '.visualizacao',
      previewTemplate : $('.preview').html(),
      init: function() {

        this.on("complete", function(file) {
          $(".dz-remove").html("<span class='upload__remove'><i class='fa fa-trash' aria-hidden='true'></i></span>");
          $('#file-dropzone').appendTo('.visualizacao')
        });

        this.on('completemultiple', function(file, json) {

          // $('.sortable').sortable({
          // 	items: '.dz-image-preview',
          // });

          if (this.files.length > 6) {
            this.removeFile(this.files[0]);
          }

        });

        // $('.uploud-photo').draggable( "disable" )
        this.on('success', function(file, json) {

        });

        this.on('addedfile', function(file) {

        });



        this.on("reset", function (file) {
          $('#file-dropzone').show()

        });

        this.on('drop', function(file) {
        });
      }
    });
  </script>
@endsection