<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PageContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Page::where("title", "Контакты")->first()->update(["content" => '
        <div class="row">
    <div class="col-lg-5 contacts-info">
    <div class="contacts-list">
        <a class="contacts-list-item contacts-list-item-tel" href="tel:+74955996674"> +7 (495) 599 66 74</a>
        <a class="contacts-list-item contacts-list-item-email" href="mailto:info@gorooms.ru"> info@gorooms.ru</a>
        <a class="contacts-list-item contacts-list-item-address"> 111558, Москва, Напольный проезд, 10</a>
        <br>
    </div>
   
    <div class="text-section">
        <h1 class="section-title">Реквизиты</h1>
        <b>ООО «Го Румс»</b> <br>
        111558, Москва, Напольный проезд, 10 <br>
        <p>
        ОГРН: 1217700386738
        &nbsp; &nbsp; &nbsp;
        ИНН: 7720853164
        &nbsp; &nbsp; &nbsp;
        КПП: 772001001
        </p>
        <hr>
        Расчетный счет: 40702810100000193243 <br>
        <p>
            Наименование банка: АО "Райффайзенбанк", г.Москва<br>
            Корреспондентский счет: 30101810200000000700 <br>
            БИК: 044525700
            &nbsp; &nbsp;
            ИНН: 7744000302
            &nbsp; &nbsp;
            КПП: 770201001
        </p>
        </div>
    </div>
    <div class="col-lg-7 contacts-info" style="margin-right: 0; padding-right: 0">
        <div class="contacts-list">
            <iframe 
            src="https://yandex.ru/map-widget/v1/?um=constructor%3A7093c7fe3b67dfb23a76afe92eb5b652bdb2db6520236a5a12300683599fa132&amp;source=constructor" 
            width="900"  
            height="440" 
            frameborder="0"
            ></iframe>        
        </div>
    </div>
    <form id="contacts-form" class="col-lg-12 form-section" action="/form" method="post" style="margin: 0 15px">
    <h2 class="section-title">Обратная связь</h2>
    <div class="row">
    <div class="col-lg-4">
    <div class="form-group"><input id="contacts-name" class="form-control form-control-border" name="contacts-name" required="" type="text" placeholder="Имя*" /></div>
    </div>
    <div class="col-lg-4">
    <div class="form-group"><input id="contacts-tel" class="form-control form-control-border" name="contacts-tel" required="" type="tel" placeholder="Тел:* +7 (___) ___ __ __" /></div>
    </div>
    <div class="col-lg-4">
    <div class="form-group"><input id="contacts-email" class="form-control form-control-border" name="contacts-email" type="email" placeholder="E-mail" /></div>
    </div>
    </div>
    <div class="row">
    <div class="offset-xl-4 col-xl-8">
    <div class="form-group"><textarea id="contacts-comment" class="form-control form-control-border" name="contacts-comment" required="" placeholder="Сообщение*"></textarea></div>
    <p class="form-disclaimer">Нажимая &laquo;Отправить&raquo; Вы даете согласие на&nbsp;обработку персональных данных и&nbsp;соглашаетесь c&nbsp;<a href="../../../privacy-policy">пользовательским соглашением и&nbsp;политикой конфиденциальности</a>.</p>
    <button class="btn btn-blue btn-big" type="submit">Отправить</button></div>
    </div>
    </form>
']);
    }
}
