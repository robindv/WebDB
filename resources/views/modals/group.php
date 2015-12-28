<?php

echo Form::open(['id'=>'modal_form','class'=>'form-horizontal']);

echo Form::b_text($errors, 'name', 'Naam', $group->name, 2);

echo Form::b_dropdown($errors, 'assistant_id', 'Assistent', $assistants, $group->assistant_id, 3);

echo Form::b_dropdown($errors, 'project', 'Project', $projects, $group->project_id, 3);

echo Form::b_textarea($errors, 'remark', 'Opmerkingen', $group->remark);

echo Form::close();