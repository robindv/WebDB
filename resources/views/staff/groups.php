<?php

echo '<h2>Groepen</h2>';

if($groups->isEmpty())
{
    echo '<p>Er zijn nog geen groepen gevonden, iemand moet zijn werk nog doen..</p>';
}
else
{
    echo '<script type="text/javascript">
        $(function() {
            $("table").tablesorter({
                theme: \'default\',
                cssChildRow: "tablesorter-childRow",
                widgets: ["saveSort", "zebra"],
                headers: { 3:{sorter:false},4:{sorter:false}, 6:{sorter:false} },
                sortReset      : true,
                sortRestart    : true,
                widgetOptions : { filter_saveFilters : true } });

        });
    </script>';

    echo link_to('staff/groups-export/webdb-groepen.csv',"CSV-export",['class'=>'pull-right btn btn-default']);

    echo '<table class="tablesorter">';
    echo '<thead><tr><th>Groep</th><th>Assistent</th><th>Project</th><th>Student</th><th>Opmerkingen</th><th>&nbsp;</th></tr></thead><tbody>';
    foreach($groups as $group)
    {
        echo '<tr id="'.$group->id.'">';

        if($group->students)
        {
            $emails = [];
            foreach($group->students as $student)
                $emails[] = $student->user->email;

            echo '<td>'.link_to('mailto:'.implode(",",$emails),$group->name).'</td>';
        }
        else
            echo '<td>'.$group->name.'</td>';

        if($group->assistant)
            echo '<td>'.link_to('mailto:'.$group->assistant->email,$group->assistant->name).'</td>';
        else
            echo '<td>Onbekend</td>';

        if($group->project && $group->server)
            echo '<td>'.link_to('http://'.$group->server->hostname, $group->project->name).'</td>';
        else
            echo '<td>Onbekend</td>';

        echo '<td colspan="1">&nbsp;</td>';

        echo '<td>'.($group->remark ? $group->remark : "&nbsp;").'</td>';

        echo '<td>'.link_to('staff/group-modal/'.$group->id,'',['class'=>'glyphicon glyphicon-pencil','data-toggle'=>'modal','data-target'=>'#modal']);
        echo '</tr>';

        if($group->students)
        {
            foreach($group->students as $student)
            {
                echo '<tr class="tablesorter-childRow"><td colspan="3">&nbsp;</td>';
                echo '<td>'.link_to('staff/students/#'.$student->id,$student->user->name).'</td>';

                echo '<td>'.($student->remark ? $student->remark : "&nbsp;").'</td>';

                echo '<td>&nbsp;</td>';
                echo '</tr>';
            }
        }
    }
    echo '</tbody></table>';
}
