<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Settings;

class AddSeoSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $setting = Settings::where('option', 'seo_/')->first();
        if (!$setting) {
            Settings::create([
                'option' => 'seo_/',
                'value' => '
            <h1 class="section-title orange">отели</h1>
<p>Разнообразный и богатый опыт дальнейшее развитие различных форм деятельности позволяет выполнять важные задания по разработке модели развития. Равным образом укрепление и развитие структуры способствует подготовки и реализации систем массового участия. Таким образом укрепление и развитие структуры позволяет оценить значение систем массового участия. С другой стороны дальнейшее развитие различных форм деятельности требуют определения и уточнения систем массового участия. Не следует, однако забывать, что сложившаяся структура организации позволяет выполнять важные задания по разработке дальнейших направлений развития. Значимость этих проблем настолько очевидна, что начало повседневной работы по формированию позиции требуют от нас анализа новых предложений.</p>
<h1 class="section-title orange">&nbsp;</h1>
<p>Разнообразный и богатый опыт дальнейшее развитие различных форм деятельности позволяет выполнять важные задания по разработке модели развития. Равным образом укрепление и развитие структуры способствует подготовки и реализации систем массового участия. Таким образом укрепление и развитие структуры позволяет оценить значение систем массового участия. С другой стороны дальнейшее развитие различных форм деятельности требуют определения и уточнения систем массового участия. Не следует, однако забывать, что сложившаяся структура организации позволяет выполнять важные задания по разработке дальнейших направлений развития. Значимость этих проблем настолько очевидна, что начало повседневной работы по формированию позиции требуют от нас анализа новых предложений.</p>
            '
            ]);
        }

        $setting = Settings::where('option', 'seo_/hotels')->first();
        if (!$setting) {
            Settings::create([
                'option' => 'seo_/hotels',
                'value' => 'HTML код, который можно писать в админке.'
            ]);
        }

        $setting = Settings::where('option', 'seo_/rooms')->first();
        if (!$setting) {
            Settings::create([
                'option' => 'seo_/rooms',
                'value' => 'HTML код, который можно писать в админке.'
            ]);
        }

        $setting = Settings::where('option', 'seo_/rooms/hot')->first();
        if (!$setting) {
            Settings::create([
                'option' => 'seo_/rooms/hot',
                'value' => 'HTML код, который можно писать в админке.'
            ]);
        }

        $setting = Settings::where('option', 'seo_/rooms/hot')->first();
        if (!$setting) {
            Settings::create([
                'option' => 'seo_/rooms/hot',
                'value' => 'HTML код, который можно писать в админке.'
            ]);
        }

        $page = \App\Models\PageDescription::where('url', '/')->first();

        if (!$page) {
            \App\Models\PageDescription::create([
                'url' => '/',
                'title' => 'Главная страница',
                'meta_description' => '',
                'meta_keywords' => '',
                'description' => '',
            ]);
        }


        $page = \App\Models\PageDescription::where('url', '/rooms/')->first();
        if (!$page) {
            \App\Models\PageDescription::create([
                'url' => '/rooms/',
                'title' => 'Комнаты',
                'meta_description' => '',
                'meta_keywords' => '',
                'description' => '',
                'model_type' => 'App\Models\Room',
            ]);
        }


        $page = \App\Models\PageDescription::where('url', '/rooms/hot/')->first();
        if (!$page) {
            \App\Models\PageDescription::create([
                'url' => '/rooms/hot/',
                'title' => 'Топ комнат',
                'meta_description' => '',
                'meta_keywords' => '',
                'description' => '',
                'model_type' => 'App\Models\Room',
            ]);
        }

    }
}
