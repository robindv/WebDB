<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Input, Form;

class FormMacrosServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Form::macro('form_group',function($errors,$name,$title, $required = false)
        {
            $size = 2;

            if($errors && $errors->has($name))
                return '<div class="alert alert-danger form-group" style="margin-bottom:10px; margin:5px;"><p>'.$errors->first($name).'</p>'.Form::rawLabel($name,$title.($required ? '<em>*</em>' : ""), ['class'=>'col-sm-'.$size.' control-label']);
            else
                return '<div class="form-group">'.Form::rawLabel($name,$title.($required ? '<em>*</em>' : ""), ['class'=>'col-sm-'.$size.' control-label']);
        });

        Form::macro('rawLabel', function($name, $value = null, $options = [])
        {
            $label = Form::label($name, '%s', $options);

            return sprintf($label, $value);
        });

        Form::macro('b_textarea', function($errors, $name, $title, $default, $size = 5, $rows = 10)
        {

            return Form::form_group($errors, $name, $title).'<div class="col-sm-'.$size.'">'.Form::textarea($name,Input::old($name,$default),['class'=>'form-control','rows'=>$rows]).'</div></div>';
        });

        Form::macro('b_text', function($errors, $name, $title, $default, $size = 5)
        {
            return Form::form_group($errors, $name, $title).'<div class="col-sm-'.$size.'">'.Form::text($name,Input::old($name,$default),['class'=>'form-control']).'</div></div>';
        });


        Form::macro('b_dropdown', function($errors, $name, $title, $options, $default, $size = 5)
        {
            return Form::form_group($errors, $name, $title).'<div class="col-sm-'.$size.'">'.Form::select($name,$options,Input::old($name,$default),['class'=>'form-control','style'=>'width:100%']).'</div></div>';
        });

        Form::macro('b_static', function($label, $value)
        {
            $size = 2;

            return '<div class="form-group">'.Form::rawLabel($label,$label, ['class'=>'col-sm-'.$size.' control-label']).'<div class="col-sm-'.(12-$size).'"><p class="form-control-static">'.$value.'</p></div></div>';
        });

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
