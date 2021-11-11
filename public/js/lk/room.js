/*
 * Copyright (c) 2021.
 *  This code is the property of the Fulliton developer.
 *  Write all questions and suggestions on the Vkontakte social network https://vk.com/fulliton
 */

/**
 * Редактирование комнаты по кнопке
 */
function allowedEditRoom ()  {
  $(this).parents('.shadow').find(".show-all").removeClass('show-all_disabled')
  $(this).parents('.shadow').find('.quote__read').show()
  $(this).parents('.shadow').find('.quote__status').hide()
  $(this).parents('.shadow').removeClass('shadow-complete')
  $(this).parents('.shadow').find('.row__head').hide()
  $(this).parents('.shadow').find('.caption-block').show();
  $(this).parents('.shadow').find('.room-details').show();
  $(this).parents('.shadow').find('.more-details').show();
  $(this).parents('.shadow').find('.hours__field').prop('disabled', false)
  $(this).parents('.shadow').find('.upload__remove').show()
  $(this).parents('.shadow').find('.sortable').sortable('enable');
  $(this).parents('.shadow').find('.uploud-photo').show()
  $(this).parents('.shadow').find('.save-room').show()

  let shadow = $(this).parents('.shadow')
  showPeriodsInShadow(shadow)


  blockSaveRoom(shadow)

  $(shadow).find('input').change(blockSaveRoom.bind(null, shadow))

  $(this).hide()
}

/**
 * Сохранение комнаты, запрос backend
 */
function saveRoom () {
  if ($(this).parents('.shadow').find('.dz-image-preview').length !== 0 || $(this).parents('.shadow').attr('data-moderate') === 'moderate') {
    let shadow = $(this).parents('.shadow').get(0)

    let number = $(shadow).find('input[name=number]').val()
    let order = $(shadow).find('input[name=order]').val()
    let name = $(shadow).find('input[name=name]').val()
    let category = $(shadow).find('input[name=category_id]').val()
    axios.post($(shadow).find('input[name=url]').val(), {
      id: shadow.dataset.id,
      number,
      order,
      name,
      category,
      types: $(shadow).find('input[name^=type]').map(function () {
        return {
          id: this.dataset.id,
          data: $(this).val(),
          value: $('#value-' + shadow.dataset.id + '-' +this.dataset.id).val()
        }
      }).get()
    })
      .then(response => {
        if (response.data.success) {
          saveFrontData.call(this)
          if (response.data.room.moderate) {
            $(shadow).find('.row__head')
              .removeClass('row__head_blue')
            $(shadow).find('.quote__status')
              .text('Проверка модератором')
            $(shadow).find('.quote__status')
              .removeClass('quote__status_blue')
              .addClass('quote__status_red')
          }
        }
      })
      .catch(error => {
        console.log(error.response)
        if (error.response.data.errors) {
          let str = '';
          Object.keys(error.response.data.errors).forEach(key => {
            str += error.response.data.errors[key][0] + ' \n'
          })

          alert(str)
        } else {
          alert('Ошибка в сохранении комнаты')
        }
      })

  }
}

/**
 * Сохранение комнаты, фронт
 */
function saveFrontData (save = false) {
  if ($(this).parents('.shadow').find('.dz-image-preview').length !== 0 ||
    $(this).parents('.shadow').attr('data-moderate') === 'moderate' ||
    save
  ) {

    let shadow = $(this).parents('.shadow')
    $(shadow).find('.remove-btn').hide()
    $(this).hide()
    $(shadow).find(".show-all").addClass('show-all_disabled')
    $(shadow).find('.quote__read').show()
    $(shadow).find('.row-status').show()
    $(shadow).addClass('shadow-complete')
    $(shadow).find('.row__head').css('display', 'flex');
    $(shadow).find('.caption-block').hide();
    $(shadow).find('.room-details').hide();
    $(shadow).find('.more-details').hide();
    $(this).parents('.shadow-complete')
      .find('.hours__field')
      .prop('disabled', true)
    $(shadow).find('.quote__status').show()
    $(shadow).find('.upload__remove').hide()
    $(shadow).find('.sortable').sortable('disable');
    $(shadow).find('.uploud-photo').hide()

    let number = $(shadow).find('input[name=number]').val()
    let order = $(shadow).find('input[name=order]').val()
    let name = $(shadow).find('input[name=name]').val()
    let category = $(shadow).find('input[name=category_id]')
      .siblings('.select__top')
      .find('.select__current')
      .text()
    category = category === 'Категория' ? '' : category

    hidePeriodsInShadow(shadow);

    $($(shadow).find('.head-text').get(0)).html('#' + order)
    $($(shadow).find('.head-text').get(1)).html('№' + number)
    $($(shadow).find('.head-text').get(2)).html(name)
    $($(shadow).find('.head-text').get(3)).html(category)
  }
}

/**
 * Удаление комнаты фронт + бек
 */
function removeRoom () {
  let shadow = $(this).parents('.shadow').get(0)
//  axios
  let url = $(shadow).find('input[name=url-delete]').val()
  let id = shadow.dataset.id
  axios.delete(url)
    .then(response => {
      if (response.data.success) {
        shadow.remove()
        if (typeof existFile !== 'undefined') {
          delete existFile[id]
        }
        if (typeof uploader !== 'undefined') {
          delete uploader[id]
        }

        if ($('#rooms').find('.shadow').length === 0) {
          document.location.reload();
        }
      }
    })
    .catch(error => {
      alert('Error server side')
      console.log(error)
    })
}

/**
 * Создание новой комнаты фронт + бек
 */
function createRoom () {
  let hotel_id = $('input[name=hotel_id]').val()
  axios.post('/lk/room/create', {
    hotel_id
  })
    .then(response => {
      console.log(response.data)
      if (response.data.success) {
        let room = $('#new_room').clone();
        let rooms = $('#rooms')

        $(room).removeAttr('id')
        let roomId = response.data.room.id
        room.attr('data-id', roomId)

        rooms.prepend(room);
        room.removeClass('d-none')
        let select_item = $('.select__item')
        let select_top = $('.select__top')
        select_item.attr("onclick", "").unbind("click")
        select_top.attr("onclick", "").unbind("click")

        select_item.bind('click', selectItem)
        select_top.bind('click', selectTop)

        $('input.has-validate-error').keyup(validateErrorBootstrap)
        $('input.has-validate-error').unbind('onclick').click(validateErrorBootstrap)

        $('.sortable').sortable({
          items: '.dz-image-preview',
        });

        $('.save-room').unbind("click").bind('click', saveRoom)

        $('.quote__read').unbind("click").bind('click', allowedEditRoom)

        $('.quote__remove').unbind("click").bind('click', removeRoom)

        let urlDelete = $(room).find('input[name=url-delete]').val()

        $(room).find('input[name=url-delete]').val(urlDelete + '/' + roomId)

        let urlAttrGet = $(room).find('input[name=attributes-get]').val()
        $(room).find('input[name=attributes-get]').val(urlAttrGet + '/' + roomId)

        let urlAttrPut = $(room).find('input[name=attributes-put]').val()
        $(room).find('input[name=attributes-put]').val(urlAttrPut + '/' + roomId)

        $('#orderRoom').removeAttr('id').attr('id', 'orderRoom-' + roomId)
        $('label[for=orderRoom]').removeAttr('for').attr('for', 'orderRoom-' + roomId)

        $('#nameRoom').removeAttr('id').attr('id', 'nameRoom-' + roomId)
        $('label[for=nameRoom]').removeAttr('for').attr('for', 'nameRoom-' + roomId)

        $('#numberRoom').removeAttr('id').attr('id', 'numberRoom-' + roomId)
        $('label[for=numberRoom]').removeAttr('for').attr('for', 'numberRoom-' + roomId)

        $(room).find('#file-dropzone').addClass('file-dropzone').attr('data-id', roomId)
        $(room).find('.visualizacao').addClass('visualizacao-' + roomId)

        let show_all = $(room).find('.show-all')
        $(show_all).attr('data-room-id', roomId)

        $(show_all).unbind('click').bind('click', openPopupAttributes)

        $(room).find('li.hour').each(function () {
          $(this).find('input#value').removeAttr('id').attr('id', 'value-' + roomId + '-' + this.dataset.id)
        })
        let zone = $(room).find('.file-dropzone').get(0)
        if (uploader)
          uploader[roomId] = new Dropzone(zone, initialDropZone.call(zone))

        if (existFile)
          existFile[roomId] = []
      }
    })
}