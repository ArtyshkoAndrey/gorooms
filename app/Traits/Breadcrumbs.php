<?php
/**
 * Created by PhpStorm.
 * User: walft
 * Date: 01.08.2020
 * Time: 0:00
 */

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\MorphOne;

trait Breadcrumbs {

    public function get_bread()
    {
        $url = $_SERVER['REQUEST_URI'];
        $urls = explode('/', $url);
         
        //Хлебные крошки
        $crumbs = array();
 
        //На главной не показываем
        if (!empty($urls))
        {
            
            $return_url_with_desc= array();
            foreach ($urls as $url)
            {
                
                if(empty($url))
                {
                    array_push($return_url_with_desc, ['url'=>'/', 'text' =>'Главная страница']);
                }

                //Прописываем название пункта, исходя из url
                switch ($url)
                {
                    case 'hotels' :
                             array_push($return_url_with_desc, ['url'=>$url, 'text' =>'Отели']);
                        break;
                   
                }
                
            }
        }
        
         $Breadcrumbs_data=$return_url_with_desc;
       return $Breadcrumbs_data;
    }
}
