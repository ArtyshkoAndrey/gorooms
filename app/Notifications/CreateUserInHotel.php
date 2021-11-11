<?php

namespace App\Notifications;

use App\User;
use App\Models\Hotel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Notify user when created general User-Hotel
 */
class CreateUserInHotel extends Notification
{

  use Queueable;

  /**
   * Created User
   *
   * @var User
   */
  private User $user;

  /**
   * Where created user
   *
   * @var Hotel
   */
  private Hotel $hotel;

  private string $password;

  /**
   * Create a new notification instance.
   *
   * @param User   $user
   * @param string $password
   * @param Hotel  $hotel
   *
   * @return void
   */
  public function __construct (User $user, string $password, Hotel $hotel)
  {
    $this->user = $user;
    $this->password = $password;
    $this->hotel = $hotel;
  }

  /**
   * Get the notification's delivery channels.
   *
   * @param mixed $notifiable
   *
   * @return array
   */
  public function via ($notifiable): array
  {
    return ['mail'];
  }

  /**
   * Get the mail representation of the notification.
   *
   * @param mixed $notifiable
   *
   * @return MailMessage
   */
  public function toMail ($notifiable): MailMessage
  {
    return (new MailMessage)
      ->greeting('Привет!')
      ->subject('Для вас был создан аккаунт')
      ->line('Для вас был создан аккаунт в сервисе Gorooms')
      ->line('Логин: ' . $this->user->email)
      ->line('Пароль: ' . $this->password)
      ->line('Вы были привязаны к отелю "' . $this->hotel->name . '"');
  }

  /**
   * Get the array representation of the notification.
   *
   * @param mixed $notifiable
   *
   * @return array
   */
  public function toArray ($notifiable): array
  {
    return [];
  }
}
