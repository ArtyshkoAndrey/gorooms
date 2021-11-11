/*
 * Copyright (c) 2021.
 *  This code is the property of the Fulliton developer.
 *  Write all questions and suggestions on the Vkontakte social network https://vk.com/fulliton
 */


/**
 * Редактирование комнаты по кнопке
 */
function allowedEditRoom()
{
  $(this).parents('.shadow').find(".show-all").removeClass('show-all_disabled')
  $(this).parents('.shadow').find('.quote__read').show()
  $(this).parents('.shadow').find('.quote__status').hide()
  $(this).parents('.shadow').removeClass('shadow-complete')
  $(this).parents('.shadow').find('.row__head').hide()
  $(this).parents('.shadow').find('.caption-block').show();
  $(this).parents('.shadow').find('.caption-details').show();
  $(this).parents('.shadow').find('.more-details').show();
  $(this).parents('.shadow').find('.hours__field').prop('disabled', false)
  $(this).parents('.shadow').find('.upload__remove').show()
  $(this).parents('.shadow').find('.sortable').sortable('enable');
  $(this).parents('.shadow').find('.uploud-photo').show()
  $(this).parents('.shadow').find('.save-room').show()
  $(this).hide()

  let shadow = $(this).parents('.shadow')

  showPeriodsInShadow(shadow)

  blockSaveRoom(shadow)

  $(shadow).find('input').change(blockSaveRoom.bind(null, shadow))

  updateArrow()
}

/**
 * После создания категории
 * Создать пустую комнату с данной категорией
 *
 * @param {Number} room_id
 * @param {Object} category
 */
function afterCreateCategory (category, room_id)
{
  console.log(category)

  createFrontData(room_id, category)
  if ($('.arrow-up'))
    $('.arrow-up').on('click', upOrderRoom)

  if ($('.arrow-down'))
    $('.arrow-down').on('click', downOrderRoom)
  updateArrow()
}

/**
 * После обновления категории
 * Обновить фронт данные у комнаты
 *
 * @param {Object} category
 */
function afterUpdateCategory(category)
{
  console.log(category)
  let shadow = $('.shadow[data-category-id=' + category.id + ']').get(0)
  console.log(shadow)
  $(shadow).find('.category-select').find('.select__current').html(category.name)
  $(shadow).find('.quote-text__number').html(category.value)

  $($(shadow).find('.row__head').find('.head-text').get(0)).html(category.name)
  $($(shadow).find('.row__head').find('.head-text').get(1)).find('span').html(category.value)
}

/**
 * После удаления категории
 * Удалить комнату фронт
 *
 * @param {Number} id
 */
function afterRemoveCategory (id)
{
  console.log(id)
  let shadow = $('.shadow[data-category-id=' + id + ']').remove();
  if ($('.arrow-up'))
    $('.arrow-up').on('click', upOrderRoom)

  if ($('.arrow-down'))
    $('.arrow-down').on('click', downOrderRoom)
  updateArrow()
}


/**
 * После сохранения комнаты
 *
 * @param {Element} room
 */
function afterSaveRoom (room) {
  console.log(room)
}

/**
 * Создание новой комнаты фронт + бек
 */
function createRoom ()
{
  let hotel_id = $('input[name=hotel_id]').val()
  axios.post('/lk/room/create', {
    hotel_id
  })
    .then(response => {
      console.log(response.data)
      if (response.data.success) {
        createFrontData(response.data.room.id, response.data.category)
      }
    })
}

/**
 * Сохранение комнаты, запрос backend
 */
function saveRoom ()
{
  if ($(this).parents('.shadow').find('.dz-image-preview').length !== 0) {
    let shadow = $(this).parents('.shadow').get(0)

    let category = $(shadow).find('input[name=category_id]').val()
    axios.post($(shadow).find('input[name=url]').val(), {
      id: shadow.dataset.id,
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
          } else {
            $(shadow).find('.row__head')
              .addClass('row__head_blue')
            $(shadow).find('.quote__status')
              .text('Опубликовано')
            $(shadow).find('.quote__status')
              .removeClass('quote__status_red')
              .addClass('quote__status_blue')
          }

          afterSaveRoom(response.data.room)
        }
      })
      .catch(error => {
        console.log(error)
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
 *
 * @param {boolean} save
 */
function saveFrontData (save = false)
{
  if ($(this).parents('.shadow').find('.dz-image-preview').length !== 0 || save) {
    let shadow = $(this).parents('.shadow')
    $(shadow).find('.remove-btn').hide()
    $(this).hide()
    $(shadow).find(".show-all").addClass('show-all_disabled')
    $(shadow).find('.quote__read').show()
    $(shadow).find('.row-status').show()
    $(shadow).addClass('shadow-complete')
    $(shadow).find('.row__head').css('display', 'flex');
    $(shadow).find('.caption-block').hide();
    $(shadow).find('.caption-details').hide();
    $(shadow).find('.more-details').hide();
    $(this).parents('.shadow-complete')
      .find('.hours__field')
      .prop('disabled', true)
    $(shadow).find('.quote__status').show()
    $(shadow).find('.upload__remove').hide()
    $(shadow).find('.sortable').sortable('disable');
    $(shadow).find('.uploud-photo').hide()

    hidePeriodsInShadow(shadow);
  }
}

/**
 * Удаление комнаты
 */
function removeRoom ()
{
  updateArrow()
}

/**
 * Создание фронта комнаты
 *
 * @param {Number} room_id
 * @param {Object} category
 */
function createFrontData (room_id, category)
{
  console.warn(category)

  let room = $('#new_room').clone();
  let rooms = $('#rooms')

  $(room).removeAttr('id')
  room.attr('data-id', room_id)
  console.log(category.id)
  room.attr('data-category-id', category.id)

  $(room).find('.category-select').find('.select__current').html(category.name)
  $(room).find('.quote-text__number').html(category.value)

  $($(room).find('.row__head').find('.head-text').get(0)).html(category.name)
  $($(room).find('.row__head').find('.head-text').get(1)).find('span').html(category.value)

  let show_all = $(room).find('.show-all')
  $(show_all).attr('data-room-id', room_id)
  $(show_all).unbind('click').bind('click', openPopupAttributes)
  rooms.append(room)
  room.removeClass('d-none')
  $('html, body').animate({
    scrollTop: $(room).offset().top - 50
  }, 0);
  let select_item = $('.select__item')
  let select_top = $('.select__top')

  select_item.attr("onclick", "").unbind("click").bind('click', selectItem)
  select_top.attr("onclick", "").unbind("click").bind('click', selectTop)

  $('.sortable').sortable({
    items: '.dz-image-preview',
  });

  $('.save-room').unbind("click").bind('click', saveRoom)

  $('.quote__read').unbind("click").bind('click', allowedEditRoom)

  $('.quote__remove').unbind("click").bind('click', removeRoom)

  $('input.has-validate-error').keyup(validateErrorBootstrap)
  $('input.has-validate-error').unbind('onclick').click(validateErrorBootstrap)

  let urlVal = $(room).find('input[name=url-delete]').val()
  $(room).find('input[name=url-delete]').val(urlVal + '/' + room_id)

  let urlAttrGet = $(room).find('input[name=attributes-get]').val()
  $(room).find('input[name=attributes-get]').val(urlAttrGet + '/' + room_id)

  let urlAttrPut = $(room).find('input[name=attributes-put]').val()
  $(room).find('input[name=attributes-put]').val(urlAttrPut + '/' + room_id)

  $(room).find('input[name=category_id]').val(category.id)
  $('#orderRoom').removeAttr('id').attr('id', 'orderRoom-' + room_id)
  $('label[for=orderRoom]').removeAttr('for').attr('for', 'orderRoom-' + room_id)

  $('#nameRoom').removeAttr('id').attr('id', 'nameRoom-' + room_id)
  $('label[for=nameRoom]').removeAttr('for').attr('for', 'nameRoom-' + room_id)

  $('#numberRoom').removeAttr('id').attr('id', 'numberRoom-' + room_id)
  $('label[for=numberRoom]').removeAttr('for').attr('for', 'numberRoom-' + room_id)

  $(room).find('#file-dropzone').addClass('file-dropzone').attr('data-id', room_id)
  $(room).find('.visualizacao').addClass('visualizacao-' + room_id)

  $(room).find('li.hour').each(function () {
    $(this).find('input#value').removeAttr('id').attr('id', 'value-' + room_id + '-' + this.dataset.id)
  })
  let zone = $(room).find('.file-dropzone').get(0)
  if (uploader)
    uploader[room_id] = new Dropzone(zone, initialDropZone.call(zone))

  if (existFile)
    existFile[room_id] = []
}

/**
 * Обновление местоположения
 * всех иконок для Order
 */
function updateArrow ()
{
  let rooms = $('.shadow')
  let firstRoom = rooms.first()
  let lastRoom = rooms.get(rooms.length - 2)

  rooms.find('.arrow-down').show()
  rooms.find('.arrow-up').show()

  $(lastRoom).find('.arrow-down').hide()
  $(firstRoom).find('.arrow-up').hide()
}

/**
 * Переместить комнату выше
 */
function upOrderRoom ()
{
  let room = $(this).parents('.shadow').get(0)
  // let scrollPos = $(window).scrollTop();

  $('.arrow-down').hide(300)
  $('.arrow-up').hide(300)

  axios.post('/api/room/order/up/' + $(room).attr('data-id'))
    .then(response => {
      console.log(response.data)
      if (response.data.success) {
        $(room).insertBefore($(room).prev().get(0))
        $('.arrow-down').show(300)
        $('.arrow-up').show(300)
        $('html, body').animate({
          scrollTop: $(room).offset().top - 50
        }, 0);
      }
      setTimeout(() => {
        updateArrow()
      }, 300)
    })
    .catch(error => {
      alert('Ошибка смены расположения комнаты')
    })

}

/**
 * Переместить комнату ниже
 */
function downOrderRoom ()
{
  let room = $(this).parents('.shadow').get(0)

  $('.arrow-down').hide(300)
  $('.arrow-up').hide(300)

  axios.post('/api/room/order/down/' + $(room).attr('data-id'))
    .then(response => {
      console.log(response.data)
      if (response.data.success) {
        $('.arrow-down').show(300)
        $('.arrow-up').show(300)
        $(room).insertAfter($(room).next().get(0))
        $('html, body').animate({
          scrollTop: $(room).offset().top - 50
        }, 0)
      }
      setTimeout(() => {
        updateArrow()
      }, 300)
    })
    .catch(error => {
      updateArrow()
      alert('Ошибка смены расположения комнаты')
    });
}