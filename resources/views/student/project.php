<?php
echo '<h2>Project</h2>';

$group = Auth::user()->student->group;

if(!$group)
{
    echo '<p>Je bent nog niet in een groep geplaatst.</p>';
}
else
{
    $project = $group->project;

    $project_id = 0;
    if($project)
        $project_id = $project->id;

    if(!$project || !(time() > $deadline || $project->advanced))
    {
        if(!$project)
            echo "<p>Jullie hebben nog geen project gekozen. Maak een keuze en klik op 'Opslaan'.</p>";


    }

    echo Form::open(['class'=>'form-horizontal']);
    echo '<div class="form-group">';
    echo '<label class="col-sm-2 control-label">Groep</label>';
    echo '<div class="col-sm-10"><p class="form-control-static">'.$group->name.'</p></div></div>';

    echo '<div class="form-group"><label class="col-sm-2 control-label">Begeleider</label>';
    echo '<div class="col-sm-10"><p class="form-control-static">'.($group->assistant_id ? $group->assistant->name : 'Onbekend').'</p></div></div>';

    echo '<div class="form-group"><label class="col-sm-2 control-label">Project</label><div class="col-sm-3">';
    if($project && (time() > $deadline || $project->advanced))
        echo '<p class="form-control-static">'.$project->name.'</p>';
    else
        echo Form::select('project',$projects,$project_id,['class'=>'form-control']).'</>';

    echo '</div></div>';

    if(!$project || !(time() > $deadline || $project->advanced))
    {
        echo Form::submit('Opslaan',['class'=>'btn btn-default col-xs-offset-2']);
    }

    echo Form::close();

    echo '<h2>Groepsleden</h2>';
    echo '<ul>';
    foreach($group->students as $student)
    {
        echo '<li>'.$student->user->name.': '.link_to('mailto:'.$student->user->email,$student->user->email).'</li>';
    }
    echo '</ul>';

}