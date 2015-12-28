<?php

echo Form::open(['id'=>'modal_form','class'=>'form-horizontal']);

echo Form::b_static('Naam', $student->user->name);

echo Form::b_text($errors,'email', 'E-mailadres', $student->user->email);

echo Form::b_text($errors, 'programme','Opleiding', $student->programme);

echo Form::b_dropdown($errors, 'active', 'Actief', ['Nee','Ja'], $student->active, 2);

echo Form::b_dropdown($errors, 'group_id', 'Groep', $groups, $student->group_id, 2);

echo Form::b_textarea($errors,'remark','Opmerkingen', $student->remark);

echo Form::close();