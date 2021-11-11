/*
 * Copyright (c) 2021.
 * This code is the property of the Fulliton developer.
 * Write all questions and suggestions on the Vkontakte social network https://vk.com/fulliton
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
  console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

let select = true
$('.select__top').bind('click', selectTop)

$('.select__item').bind('click', selectItem)

$(document).mouseup(function (e) {
  let container = $(".select");
  if (container.has(e.target).length === 0) {
    $('.select__hidden').slideUp();
    $('.select__arrow').removeClass('open')
  }
});

/**
 * Действия при клике на чекбокс
 */
$('.check').bind('click', function () {
  if (!$(this).is('[disabled]'))
    $(this).siblings('input[type="radio"]').prop('checked', true)
})

$("body").on("click", ".agreement_check", function () {
  $(this).next("label").trigger("click");
  $(".agreement-choice").trigger("click");
});

let check = true

/**
 * Действия при клике на чекбокс
 */
$('.check').bind('click', function () {

  if (!$(this).is('[disabled]')) {
    if ($(this).siblings('input[type="checkbox"]').prop('checked')) {
      $(this).siblings('input[type="checkbox"]').prop('checked', false).trigger('change');

    } else {
      $(this).siblings('input[type="checkbox"]').prop('checked', true).trigger('change');

    }
  }
})

$('input[name="fond"]').bind('change', function () {
  if ($('#everyRoom').prop('checked')) {
    $('#fondText1').fadeIn(1)
    $('#fondText2').fadeOut(1)
  } else {
    $('#fondText2').fadeIn(1)
    $('#fondText1').fadeOut(1)
  }
})

$('.close-this').bind('click', function () {
  $('.overlay').removeClass('open')
  $(this).parent('.popup').removeClass('open')
})

$('.overlay').bind('click', function () {
  $('.overlay').removeClass('open')
  $('.popup').removeClass('open')
})

$('.staff-item_staff').bind('click', function () {
  let id = $(this).attr('data-id')
  let popup = $('.popup[data-id=' + id + ']').get(0);
  if (id === 'createUser') {
    let position = $(this).attr('data-position')
    let positionLanguage = $(this).attr('data-position-language')

    $($(popup).find('h2')).html(positionLanguage)
    $($(popup).find('input[name="hotel_position"]')).val(position)
  }
  $(popup).addClass('open')
  $('.overlay').addClass('open')
})

$('.staff-item_general').bind('click', function () {
  let id = $(this).attr('data-id')
  let popup = $('.popup[data-id=' + id + ']').get(0);

  if (id === 'createUser') {
    let position = $(this).attr('data-position')
    let positionLanguage = $(this).attr('data-position-language')

    $($(popup).find('h2')).html(positionLanguage)
    $($(popup).find('input[name="hotel_position"]')).val(position)
  }
  $(popup).addClass('open')
  $('.overlay').addClass('open')
})

$('#adressBtn').bind('click', function () {
  $('#popupAdress').addClass('open')
  $('.overlay').addClass('open')
})

$('#objectBtn').bind('click', function () {
  $('#popupObject').addClass('open')
  $('.overlay').addClass('open')
})

$('#stationBtn').bind('click', function () {
  $('#popupStation').addClass('open')
  $('.overlay').addClass('open')
})

let change = true

/**
 * Клонирование категории
 */
$('.category__add').bind('click', function () {
  $('.categories__item:last-child').clone(true, true).addClass('open').prependTo('.categories')
  event.preventDefault();
})

/**
 * Клик и режим редактирование категории
 */
$('.category-change').bind('click', function () {
  let categoryText = $(this).parents('.categories__item').find('.categories__name').text(),
    categoryQuote = $(this).parents('.categories__item').find('.categories__quote').text()

  $(this).parents('.categories__item').addClass('open')
  $(this).parents('.categories__item').find('.field_hidden-room').val(categoryText)
  $(this).parents('.categories__item').find('.field_hidden-quote').val(categoryQuote)
})

/**
 * Как на создание категории
 */
$('.category-good').bind('click', createCategory)
$("body").bind("keyup", "ul.categories .field", function (e) {
  if (e.keyCode == 13) {
    $(".category-good").trigger("click");
  };
});

/**
 * Действия на удаление категории
 */
$('.categoryRemove').bind('click', removeCategory)

/**
 * При клике на кнопку Показать всё
 */
$('.show-all').bind('click', openPopupAttributes)

/**
 * При клике на кнопку Сохранить атрибуты
 */
$('.popup__button_attributes').bind('click', savePopupAttributesRoom)

$('#orderRoom').bind('click focused bloor', function () {
  $(this).parents('.shadow').find('.caption-block .caption').slideUp(1)
  $(this).parents('.shadow').find('#orderRoomText').slideDown(1)
})

$('#numberRoom').bind('click focused bloor', function () {
  $(this).parents('.shadow').find('.caption-block .caption').slideUp(1)
  $(this).parents('.shadow').find('#numberRoomText').slideDown(1)
})

$('#nameRoom').bind('click focused bloor', function () {
  $(this).parents('.shadow').find('.caption-block .caption').slideUp(1)
  $(this).parents('.shadow').find('#nameRoomText').slideDown(1)
})

$('#selectRoom').bind('click focused bloor', function () {
  $(this).parents('.shadow').find('.caption-block .caption').slideUp(1)
  $(this).parents('.shadow').find('#selectRoomText').slideDown(1)
})

/**
 * Клик для удаления комнаты
 */
if (typeof removeRoom === 'function') {
  $('.quote__remove').bind('click', removeRoom)
}

/**
 * Клик для создания новой комнаты
 */
if (typeof createRoom === 'function') {
  $('.room__add').on('click', createRoom)
}

window.textChange = true
window.textChange2 = true

/**
 * Клик для сохранения с поля в блок
 */
$('#howRideBtn').bind('click', function () {
  // let text = $(this).parents('.part').find('.text').text()

  if (window.textChange === true) {
    // $(this).parents('.part').find('.ck-editor__main p').text(text)
    $(this).text('Сохранить')
    setTimeout(() => {
      $(this).attr('type', 'submit')
    }, 500)
    $(this).parents('.part').find('.editor__text').hide()
    $(this).parents('.part').removeClass('ck-editor_hidden')
    window.textChange = false
  } else if (textChange === false) {
    let editorText = $(this).parents('.part').find('.ck-content').html()

    // if (editorText === "") {
    //   textChange2 = false
    // } else {
    let editor_text = $(this).parents('.part').find('.editor__text')
    editor_text.show()
    editor_text.html(editorText)
    $(this).parents('.part').addClass('ck-editor_hidden')
    $(this).parents('.part').find('.ck-editor__main i')
    $(this).parents('.part').find('.ck-content').css('padding', '0')
    $(this).text('Редактировать')
    window.textChange = true

    //}

  }
})

/**
 * Клик для сохранения с поля в блок
 */
$('#howRideBtn2').bind('click', function () {
  // let text = $(this).parents('.part').find('.text').text()

  if (window.textChange2 === true) {
    // $(this).parents('.part').find('.ck-editor__main p').text(text)
    $(this).text('Сохранить')
    setTimeout(() => {
      $(this).attr('type', 'submit')
    }, 500)
    $(this).parents('.part').find('.editor__text').hide()
    $(this).parents('.part').removeClass('ck-editor_hidden')
    window.textChange2 = false
  } else if (window.textChange2 === false) {
    let editorText = $(this).parents('.part').find('.ck-content').html()

    // if (editorText === "") {
    //   textChange2 = false
    // } else {
      let editor_text = $(this).parents('.part').find('.editor__text')
      editor_text.show()
      editor_text.html(editorText)
      $(this).parents('.part').addClass('ck-editor_hidden')
      $(this).parents('.part').find('.ck-editor__main i')
      $(this).parents('.part').find('.ck-content').css('padding', '0')
      $(this).text('Редактировать')
      window.textChange2 = true

    // }
  }
})

$('.room-upload').bind('click', function () {
  let room = $(this).parents('.shadow').get(0)

  let url = $(room).find('input[name=room-published]').val()
  if (url) {
    axios.post(url)
      .then(r => {
        if (r.data.success) {
          $(room).find('.text.quote__status').text('Опубликовано').removeClass('quote__status_red')
          $(room).find('.row__head').addClass('row__head_blue')
          $(this).remove()
        }
      })
      .catch(e => {
        alert('Ошибка на стороне сервера')
      })
  }
})

$('.moderate').bind('click', function () {
  let item = $(this).parents('.uploud__item').get(0)
  if (item) {
    let url = $(item).attr('data-image-moderate')
    let id = $(item).attr('data-image-id')
    console.log(id, url)
    if (id && url) {
      axios.post(url + '/' + id)
        .then(r => {
          if (r.data.success) {
            let status = $(item).find('.uploud__status')
            status.text('Опубликовано')
            status.addClass('uploud__status_good')
            $(this).remove()
          }
        })
    } else {
      console.log(2)
      alert('Ошибка удаления фоток')
    }
  } else {
    console.log(1)
    alert('Ошибка удаления фоток')
  }
})

/**
 * Клик на удаление фотографии
 */
$('.remove-photo').bind('click', function () {
  let item = $(this).parents('.uploud__item').get(0)
  if (item) {
    let url = $(item).attr('data-image-delete')
    let id = $(item).attr('data-image-id')
    console.log(id, url)
    if (id && url) {
      axios.delete(url + '/' + id)
        .then(r => {
          if (r.data.success) {
            $(item).remove()


            if (typeof updateOrderPhotos === 'function') {
              updateOrderPhotos();
            }
          } else {
            alert(r.data.payload.error)
          }
        })
        .catch(e => { $(item).remove(); alert('Ошибка удаления фотографии') })
    } else {
      console.log(2)
      alert('Ошибка удаления фоток')
    }
  } else {
    console.log(1)
    alert('Ошибка удаления фоток')
  }

  if ($(item).parents('.shadow').length > 0) {
    let shadow = $(item).parents('.shadow');
    setTimeout(() => {
      blockSaveRoom(shadow)
    }, 1000)
  }

})

/**
 * Клик на выбор согласия
 */
$('.agreement-choice').bind('click', function () {
  let agreement = $('#agreement')

  if (agreement.prop('checked')) {
    $('#registerObject').removeAttr('disabled')
  } else if (!agreement.prop('checked')) {
    $('#registerObject').prop('disabled', true)
  }
})

/**
 * Клик на редактирование комнаты
 */
if (typeof allowedEditRoom === 'function') {
  $('.quote__read').bind('click', allowedEditRoom)
}

/**
 *Клик на сохранение комнаты
 */
if (typeof saveRoom === 'function') {
  $('.save-room').bind('click', saveRoom)
}

/**
 * При поднятии клавиши клавиатуры в поле email
 */
// $('input[type="email"]').bind('keyup', function () {
//   let email = $(this).val();
//   if (!validateEmail(email)) {
//
//     $(this).css('border', '1px solid orange')
//
//   } else if (validateEmail(email)) {
//     $(this).css('border', '1px solid #2f64ad')
//
//   }
// })

/**
 * При поднятии клавиши клавиатуры в поле телефон
 */
// $('input[type="phone"]').bind('keyup', function () {
//   let phone = $(this).val();
//   if (!validatePhone(phone)) {
//
//     $(this).css('border', '1px solid orange')
//
//   } else if (validatePhone(phone)) {
//     $(this).css('border', '1px solid #2f64ad')
//
//   }
// })

/**
 * Валидация Email
 *
 * @param {String} email
 */
function validateEmail(email) {
  let emailReg = /^([a-zA-Z0-9_.\-+])+@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return emailReg.test(email);
}

/**
 * Валидация телефона
 *
 * @param {String} phone
 */
function validatePhone(phone) {
  let regex = /^(\+7|7|8)?[\s\-]?\(?[489][0-9]{2}\)?[\s\-]?[0-9]{3}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$/;
  return regex.test(phone);
}

/**
 * Нахождение нужного файла из дроп зоны
 *
 * @param {File} file
 * @param {Array} files
 */
function findExistImage(file, files) {
  return files.filter(x => {
    if (file.xhr) {
      let image = JSON.parse(file.xhr.response).payload.images[0]
      console.log(window.location.origin + "/" + image.path)
      return (window.location.origin + "/" + image.path) === x.path
    } else {
      console.log(1)
      return x.path === file.dataURL
    }
  }).pop()
}

/**
 * Действия при клике на итем в меню
 */
function selectItem() {
  if ($(this).attr('disable') !== true) {
    $(this).siblings('.select__item').removeClass('active')
    $(this).addClass('active')
    let input = $(this).parent('.select__hidden').siblings('input[type="hidden"]')

    $(input).val(this.dataset.id)
    $(input).change();

    $(this).parent('.select__hidden').slideUp()
    $(this).parent('.select__hidden').siblings('.select__top').find('.select__current').text($(this).text())
    $(this).parent('.select__hidden').siblings('.select__top').find('.select__arrow').removeClass('open')
  }

  console.log($(this).parents('.select').first().hasClass('has-validate-error-select'), $(this).parents('.select'))

  if ($(this).parents('.select').first().hasClass('has-validate-error-select')) {
    $(this).parents('.select').first().find('.select__top').removeClass('is-invalid form-control')
  }
}

/**
 * Действия при удалении категории
 */
function removeCategory() {

  let item = $(this).parents('.categories__item').get(0);

  let id = item.dataset.id
  if (id) {
    let url = $('[name="category.delete"]').get(0)
    if (url) {
      url = url.value + '/' + id

      axios.delete(url)
        .then(response => {
          if (response.data.status === 'error') {
            alert('Ошибка сохранения')
          } else {
            $(item).remove()

            $('li.select__item[data-id=' + id + ']').remove()

            if (typeof afterRemoveCategory === 'function') {
              afterRemoveCategory(id)
            }

            if (response.data.reload) {
              location.reload();
            }
          }
        })
        .catch(error => {
          console.error(error)
        })
    }
  } else {
    $(item).remove()
  }


  event.preventDefault();

}

/**
 * Действия при создании категории
 */
function createCategory() {

  let categoryVal = $(this).parents('.categories__item').find('.field_hidden-room').val()
  let categoryValQuote = $(this).parents('.categories__item').find('.field_hidden-quote').val()

  if (categoryVal === "" || categoryValQuote === "") {

  } else {
    let item = $(this).parents('.categories__item').get(0)
    let oldVal = $(item).find('.categories__name').get(0).innerText
    console.log(oldVal)
    $(item).removeClass('open')
    $(item).find('.categories__name').text(categoryVal)
    $(item).find('.categories__quote').text(categoryValQuote)
    let id = item.dataset.id
    // Если есть id то редактировать
    if (id) {
      let url = $('[name="category.update"]').get(0)
      if (url) {
        url = url.value
        let hotel_id = $('[name="hotel_id"]').get(0).value

        axios.put(url, {
          id: id,
          name: categoryVal,
          hotel_id: hotel_id,
          value: categoryValQuote ? categoryValQuote : null
        })
          .then(response => {
            if (response.data.status === 'error') {
              $(item).find('.categories__name').text(oldVal)
              alert('Ошибка сохранения')
            } else {

              let ul = $('.category__list')
              ul.each(function (i) {
                let li = $(this).find('li')
                li.each(function () {
                  console.log($.trim($(this).text()))
                  if ($.trim($(this).text()) === oldVal) {
                    $(this).text(categoryVal)
                  }
                })
              })
              if (typeof afterUpdateCategory === 'function') {
                afterUpdateCategory({
                  name: categoryVal,
                  value: categoryValQuote,
                  id
                })
              }
            }
          })
          .catch(error => {
            console.error(error)
            $(item).find('.categories__name').text(oldVal)
            if (error.response.data.errors) {
              error.response.data.errors.name.forEach(e => {
                alert(e)
              })
            }
          })
      } else {
        alert('Ошибка сохранения')
      }

      //  Если нет то создавать
    } else {
      let url = $('[name="category.create"]').get(0)
      let hotel_id = $('[name="hotel_id"]').get(0).value
      if (url) {
        url = url.value

        axios.post(url, {
          name: categoryVal,
          value: categoryValQuote ? categoryValQuote : null,
          hotel_id: hotel_id
        })
          .then(response => {
            if (response.data.status === 'error') {
              alert('Ошибка сохранения')
              $(item).find('.categories__name').text(oldVal)
            } else if (response.data.category) {
              console.log(response.data.category)
              let category = response.data.category
              item.dataset.id = category.id

              if (typeof afterCreateCategory === 'function') {
                afterCreateCategory(category, response.data.room.id);
              }

              let ul = $('.category__list')

              ul.each(function (i) {
                let li = $($(ul).find('li').get(0)).clone()
                $(li).attr('disable', false)
                if (li.length === 0) {
                  li = document.createElement('li')
                  $(li).addClass('select__item')
                }
                $(li).appendTo($(this))
                console.log(li, this)
                $(li).attr("data-id", category.id)
                $(li).removeClass('active')
                $(li).text(category.name)
                $(li).on('click', selectItem)
              })

            }
          })
          .catch(error => {
            console.error(error)
            $(item).find('.categories__name').text(oldVal)
            if (error.response.data.errors) {
              error.response.data.errors.forEach(e => {
                alert(e)
              })
            }
          })
      } else {
        alert('Ошибка сохранения')
      }
    }
  }

}

/**
 * Открывает выпадающие меню списка
 */
function selectTop() {
  $('.select__arrow').not($(this).find('.select__arrow')).removeClass('open')
  $(this).find('.select__arrow').toggleClass('open')
  $('.select__hidden').not($(this).siblings('.select__hidden')).slideUp(1)
  $(this).siblings('.select__hidden').slideToggle()
}

/**
 * Открывает модалку с атрибутами от комнаты
 */
function openPopupAttributes () {
  if ($(this).hasClass('show-all_disabled')) {
      event.preventDefault();
    }
  else {

      $(`input[name*='attr'][type='checkbox']`).prop('checked', false)

      $("input[name*='attr'][type='checkbox']").on( "change", function() {
        console.log(2);
        if (+$("input[name*='attr'][type='checkbox']:checked").length > 9)
        {
          this.checked=false;
        } else if ($("input[name*='attr'][type='checkbox']:checked").length < 3) {
          this.checked = true;
        }
      });

      let room_id = $(this).attr('data-room-id')
      let room = $(this).parents('.shadow')

      let urlAttrGet = $(room).find('input[name=attributes-get]').val()
      if (room_id) {
        axios.get(urlAttrGet)
          .then(response => {
            let attrs = response.data.attrs
            let popup = $('#popupDetails');

            let input = popup.find('input[name="room_id"]')

            $(input).val(room_id)
            attrs.forEach(attr => {
              $('#attr-' + attr.id).prop('checked', true);
            })


            popup.addClass('open')
            $('.overlay').addClass('open')
          })
          .catch(e => {
            alert('Ошибка отображения атрибутов')
          })
      }
}
}

/**
 * Проверяет на выбранные атрибуты в комнатах и сохраняет
 */
function savePopupAttributesRoom () {
  let popup = $(this).parents('.popup ').get(0)

  let room_id = $(popup).find('input[name="room_id"]').val()

  if (room_id) {
    let attributes = $(popup).find('input[name*="attr"][type="checkbox"]:checked')
    let ids = [];
    let names = [];
    attributes.each(function () {
      console.log($(this).val())
      names.push($(this).first().attr('data-placeholder'))
      ids.push($(this).val())
    })

    if (ids.length < 3 || ids.length > 9) {
      alert('Выбрано количество не удовлетворяет требованиям')
      return 0;
    }

    backEndSaveAttributesRoom(room_id, ids, popup, names)
  } else {
    alert('Ошибка в определении выбранной комнаты')
    return 0;
  }
}

/**
 * Сохранить данные на уровне сервера
 *
 * @param {Number} room_id
 * @param {[Number]} ids
 * @param {HTMLElement} popup
 * @param {[String]} names
 */
function backEndSaveAttributesRoom (room_id, ids, popup, names) {
  let room = $('.shadow[data-id=' + room_id + ']')

  let urlAttrPut = $(room).find('input[name=attributes-put]').val()

  axios
    .put(urlAttrPut, {
      ids
    })
    .then(r => {
      if (r.data.success) {
        let list = $('.shadow[data-id=' + room_id + ']').find('.attributes-list')
        list = $(list)
        list.empty()
        names.forEach((name, index) => {
          if (names.length !== index + 1) {
            list.append('<span>' + name + ', </span>')
          } else {
          list.append('<span>' + name + '</span>')
          }
        })

        $('.shadow[data-id=' + room_id + ']').find('.more-details').find('p.text').removeClass('is-invalid form-control')

        if (r.data.room.moderate) {
          $(room).find('.row__head')
            .removeClass('row__head_blue')
          $(room).find('.quote__status')
            .text('Проверка модератором')
          $(room).find('.quote__status')
            .removeClass('quote__status_blue')
            .addClass('quote__status_red')
        }

        $(popup).find('.close-this').click()
      }
    })
    .catch(e => {
      alert('Ошибка в сохранении атрибутов')
    })
}

/**
 * Валидация полей
 *
 */
function validateErrorBootstrap() {
  let el = $(this)
  if (el.val().trim() === '') {
    el.addClass('is-invalid form-control')
  } else {
    el.removeClass('is-invalid form-control')
  }
}

/**
 * Прячет периоды при сохранении
 *
 * @param {HTMLElement} shadow
 */
function hidePeriodsInShadow(shadow) {
  $(shadow).find('.hours__select').hide();
  let i = 0;
  $(shadow).find('.hours__select').each(function () {
    let text = $(this).find('.select__current').text()
    $($(shadow).find('.hours__after').get(i)).text(text)
    i++;
  })
  $(shadow).find('.hours__after').show();
}

/**
 * Показывает время периодов
 *
 * @param {HTMLElement} shadow
 */
function showPeriodsInShadow(shadow) {
  $(shadow).find('.hours__after').hide();
  $(shadow).find('.hours__select').show();
}

function blockSaveRoom (shadow) {
  let flag = true;
  console.log(shadow)
  $(shadow).find('input').each( function () {
    if ($(this).val().trim() === '') {
      flag = false
    }
  })

  if( $(shadow).find('.visualizacao').find('li').length < 1) {
    flag = false
  }

  if (flag) {
    $(shadow).find('#saveRoom').removeAttr('disabled');
  } else {
    $(shadow).find('#saveRoom').prop("disabled", true);
  }
}

let arrow_up = $('.arrow-up')
let arrow_down = $('.arrow-down')

setTimeout( () => {
  if (arrow_up.length > 0) {
    arrow_up.on('click', upOrderRoom)
  }
  if (arrow_down.length > 0) {
    arrow_down.on('click', downOrderRoom)
  }

}, 400)


if (typeof updateArrow === 'function') {
  updateArrow()
}

$('input.has-validate-error').keyup(validateErrorBootstrap)

$('input.has-validate-error').click(function () {
  $(this).keyup()
})

$('input.has-validate-error').each(validateErrorBootstrap)

$('div.has-validate-error-select').each(function () {
  let input = $(this).find('input')
  input = $(input)

  if (input.val() === '') {
    $(this).find('.select__top').addClass('form-control is-invalid')
  } else {
    $(this).find('.select__top').removeClass('form-control is-invalid')
  }
})


/**
 * Обновление порядка фотографий для модераторов
 *
 */
function updateOrderPhotos () {
  let ids = [];
  $(".uploud li").each(function(i) {
    ids.push($(this).attr('data-image-id'))
  });
  console.log(ids)

  axios.post('/api/images/ordered', {
    ids
  })
    .catch(e => {
      if (e.response.data.message) {
        alert(e.response.data.message)
      }
    })
}