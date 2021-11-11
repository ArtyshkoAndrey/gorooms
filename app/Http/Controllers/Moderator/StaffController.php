<?php /** @noinspection ALL */

/*
 * Copyright (c) 2021.
 * This code is the property of the Fulliton developer.
 * Write all questions and suggestions on the Vkontakte social network https://vk.com/fulliton
 */

namespace App\Http\Controllers\Moderator;

use App\User;
use Exception;
use App\Models\Hotel;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Swift_TransportException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Moderate\StaffRequest;
use Illuminate\Contracts\View\Factory;
use App\Notifications\CreateUserInHotel;
use App\Notifications\UpdateRandomPasswordUser;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StaffController extends Controller
{

  /**
   * All Users in Hotel
   *
   * @return Application|Factory|View
   * @noinspection PhpVariableNamingConventionInspection
   */
  public function index (int $id)
  {
    $hotel = Hotel::findOrFail($id);

    /**
     * Length general users
     *
     * @var int $userGeneralCount
     */
    $userGeneralCount = $hotel->users()->wherePivot('hotel_position', User::POSITION_GENERAL)->count();

    /**
     * Length staff users
     *
     * @var int $userStaffCount
     */
    $userStaffCount = $hotel->users()->wherePivot('hotel_position', User::POSITION_STAFF)->count();

    /**
     * All users in hotel
     *
     * @var User[] $users
     */
    $users = $hotel->users()->withPivot(['hotel_position'])->get();

    return view('moderator.staff.index', compact('users', 'userGeneralCount', 'userStaffCount', 'hotel'));
  }

  /**
   * Delete user in system
   *
   * @param int $staff_id
   *
   * @return RedirectResponse
   */
  public function remove (int $staff_id): RedirectResponse
  {
    try {
      $user = User::findOrFail($staff_id);

      //    Если у удаляемого юзера прописано что он создатель отеля то...

      if ($user->id !== auth()->id()) {
        if ($user->hotel()->exists()) {

          //      Берём его отель
          $hotel = $user->hotel;

          if (!$this->check_last_general($hotel, $user)) {
            return back()->with('error', 'Пользователь не удалён. Так как в отеле больше нету Главных');
          }
        } else {
          $hotel = Hotel::whereHas('users', function ($q) use ($user) {
            $q->where('users.id', $user->id);
          })->first();

          if (!$this->check_last_general($hotel, $user)) {
            return back()->with('error', 'Пользователь не удалён. Так как в отеле больше нету Главных');
          }
        }
        $status = $user->forceDelete();
        if ($status) {
          return back()->with('success', 'Пользователь удалён');
        }

        return back()->with('error', 'Пользователь не удалён');
      } else {
        return back()->with('error', 'Пользователь не удалён. Так как Вы им являетесь');
      }

      return back()->with('error', 'Пользователь не удалён. Так как Вы им являетесь');
    } catch (ModelNotFoundException $e) {
      return back()->with('error', 'Не удалось найти пользователя');
    } catch (Exception $e) {
      return back()->with('error', $e->getMessage());
    }

  }

  /**
   * Check exist general users in hotel.
   * If exist general, and delete own general, Hotes set new user_id
   * If not exist, not delete user
   *
   * @param Hotel $hotel
   * @param User  $user
   *
   * @return bool
   */
  private function check_last_general (Hotel $hotel, User $user): bool
  {
    //  Определяем кол-во главный в отеле
    $count_general_users_in_hotel = $hotel->users()->wherePivot('hotel_position', User::POSITION_GENERAL)->count();

    //  Если в отеле больше 1 главных то..

    if ($count_general_users_in_hotel > 1) {

      //    То берём нового юзера главного из отеля
      $new_general_user = $hotel->users()->wherePivot('hotel_position', User::POSITION_GENERAL)->wherePivotNotIn('user_id', [$user->id])->first();

      //    И отелю даём нового создателя.
      $hotel->user_id = $new_general_user->id;
      $hotel->save();

      return true;
    } else {
      return false;
    }
  }

  /**
   * Create User in Hotel
   *
   * @param StaffRequest $request
   *
   * @return RedirectResponse
   */
  public function create (int $id, StaffRequest $request): RedirectResponse
  {
    $hotel = Hotel::findOrFail($id);

    $user = new User($request->all());

    $user->password = Hash::make($request->get('password'));
    $user->is_admin = false;
    $user->is_moderate = false;
    $user->save();



    if ($hotel->users()->count() === 0 && $request->get('hotel_position') === User::POSITION_GENERAL) {
      $hotel->user()->associate($user->id);
    }


    $hotel->users()->attach($user->id, ['hotel_position' => $request->get('hotel_position')]);

    try {
      $user->notify(new CreateUserInHotel($user, $request->get('password'), $hotel));
    } catch (Swift_TransportException $e) {
      return back()->with('success', 'Пользователь успешно создан')->with('error', 'Сообщение небыло отправлено');
    }


    return back()->with('success', 'Пользователь успешно создан');
  }

  /**
   * Update user info
   *
   * @param StaffRequest $request
   * @param int          $staff_id
   *
   * @return RedirectResponse|void
   */
  public function update (StaffRequest $request, int $staff_id)
  {
    try {
      $user = User::findOrFail($staff_id);

      $user->update($request->except('password'));
      //      If user writen password, update )
      if ($request->has('password')) {
        if ($request->get('password') !== '') {
          $user->password = Hash::make($request->get('password'));
        }
      }

      $user->save();

      return back()->with('success', 'Пользователь обновлён');
    } catch (ModelNotFoundException $e) {
      return back()->with('error', 'Не удалось найти пользователя');
    }
  }

  /**
   * Save random passworn in user and notify on mail
   *
   * @param int $staff_id
   *
   * @return RedirectResponse
   */
  public function generatePassword (int $staff_id)
  {
    $user = User::findOrFail($staff_id);
    $password = Str::random(8);
    $user->password = Hash::make($password);
    $user->save();

    try {
      $user->notify(new UpdateRandomPasswordUser($user, $password));
    } catch (Swift_TransportException $e) {
      return back()->with('success', 'Пароль был сброшен и отправлен на почту сотрудника')->with('error', 'Сообщение небыло отправлено');
    }

    return back()->with('success', 'Пароль был сброшен и отправлен на почту сотрудника');
  }

}