<?php

namespace App\Http\Controllers;

use App\Jobs\BookRoomJob;
use App\Models\Booking;
use App\Models\Form;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class RoomController extends Controller
{

  public function index(Request $request): View
  {
    $sortByRequested = [
      'cost' => false,
    ];

    if ($request->has('cost')) {
      $sortByRequested['cost'] = $orderCost = $request->input('cost');

      $rooms = Room::orderByCost($orderCost)->paginate(18);
    } else {
      $rooms = Room::paginate(20);
    }

    return view('room.index', compact('rooms', 'sortByRequested'));
  }

  public function hot(): View
  {
    $rooms = Room::hot()->paginate(30);
    $title = 'Горящие предложения';
    $hide_filter = true;
    return view('room.index', compact('rooms', 'title', 'hide_filter'));
  }

  public function show(Room $room): View
  {
    $pageAbout = $room->meta;
    return view('room.show', compact('room', 'pageAbout'));
  }

  /**
   * Забронировать номер
   *
   * @param int $id
   * @param Request $request
   * @return Response
   * @throws \JsonException
   */
  public function booking(int $id, Request $request): RedirectResponse
  {
    $validated = $request->validate([
      'book-name' => ['required', 'string',],
      'book-tel' => ['required', 'string',],
      'from-date' => ['required', 'date',],
      'from-time' => ['required', 'string',],
      'to-date' => ['required', 'date',],
      'to-time' => ['required', 'string'],
      'book-comment' => ['nullable'],
      'book_by' => ['string'],
      'order_at' => ['string'],
    ]);

    //todo form - это то, где раньше хранились заказы (и остаются лежать старый заказы), скорее всего это надо будет удалить
    $form = new Form();
    $form->page = url()->previous();
    $form->ip = $request->ip();
    $form->fields = json_encode($validated, JSON_THROW_ON_ERROR);
    $form->save();

    $validated['book_number'] = BookingController::defineNextBookNumber();
    Booking::create([
      'book_number' => $validated['book_number'],
      'client_fio' => $validated['book-name'],
      'client_phone' => $validated['book-tel'],
      'book_type' => $validated['order_at'],
      'book_comment' => $validated['book-comment'],
      'from-date' => Carbon::createFromFormat('Y-m-d H:m', $validated['from-date'] . ' ' . $validated['from-time'])->toDateTimeString(),
      'to-date' =>
        ($validated['order_at'] !== 'hour'
          && $validated['order_at'] !== 'night')
          ? Carbon::createFromFormat('Y-m-d H:m', $validated['to-date'] . ' ' . $validated['to-time'])->toDateTimeString()
          : null,
      'hours_count' => $validated['order_at'] === 'hour' ?
        $validated['book_by']
        : null,
    ]);

    BookRoomJob::dispatch($id, $validated);

    $room = Room::where('id', $id)->with(['hotel', 'category'/*, 'costs'*/])->first();

    $message = "Бронирование № ".$validated['book_number'].'<br><br>';
    $message .= "Вы совершили бронирование в номере<br>" . $room->name ?? $room->id;
    $message .= $room->category_id ? ' , в категории "' . $room->category->name . '"' : '';
    $message .= '<br>в объекте размещения: "' . $room->hotel->type->single_name . ': ' . $room->hotel->name . '".';
    $message .= '';
    $message .= '<br>Администратор ' . $room->hotel->name . ' свяжется с Вами в случае необходимости!
                    <br>Ждем Вас и приятного отдыха!';

    return redirect()->back()->with([
      'showSuccessModal' => true,
      'SuccessModalMessage' => $message,
    ]);
  }

  /**
   * Получить по api информацию о номере
   *
   * @param int $id
   * @return JsonResponse
   */
  public function getRoomInfo(int $id): JsonResponse
  {
      $room = Room::where('id', $id)->with(['hotel', 'category'/*, 'costs'*/])->first();
    return \response()->json([
        'room_info' => $room,
        'costs' => $room->costs->pluck('period')
    ]);
  }
}
