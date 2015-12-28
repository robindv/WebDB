<?php

echo Form::open(['id'=>"modal_form",'class'=>'form-horizontal']);

echo Form::b_static('Server',$server->name);

echo Form::b_text($errors, 'domain', 'Domeinnaam', "", 4);

echo Form::close();
