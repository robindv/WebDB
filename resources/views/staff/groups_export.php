<?php

echo "Groep,Assistent,Project,Student,Opmerkingen\n";
foreach($groups as $group)
{

    echo $group->name;

    if($group->assistant)
        echo ',"'.$group->assistant->name.'"';
    else
        echo ',Onbekend';

    if($group->project)
        echo ',"'.$group->project->name.'"';
    else
        echo ',Onbekend';

    echo ',';

    echo ','.($group->remark ? $group->remark : "");

    echo "\n";

    if($group->students)
    {
        foreach($group->students as $student)
        {
            echo ',,';
            echo ',"'.$student->user->name.'"';

            echo ',"'.($student->remark ? str_replace('"','""',$student->remark) : "").'"';

            echo "\n";

        }
    }

}