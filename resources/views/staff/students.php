<script type="text/javascript">
        $(function() {
            $("table").tablesorter({
                theme: 'default',
                widgets: ["saveSort", "zebra"],
                headers: { 0:{sorter:false}, 7:{sorter:false} },
                sortReset      : true,
                sortRestart    : true,
                widgetOptions : { filter_saveFilters : true } });
        });
    </script>
<?php
echo '<h2>Studenten</h2>';

echo link_to('staff/students-export/webdb-studenten.csv',"CSV-export",['class'=>'pull-right btn btn-default']);

if($users)
{
    echo '<table style="font-size:12px;" class="tablesorter">';
    echo '<thead><tr align="left"><th>Actief</th><th>UvAnetID</th><th>Naam</th><th>Opleiding</th><th>Tutor</th></th><th>Opmerking</th><th>Groep</th><th>&nbsp;</th></tr></thead><tbody>';
    foreach($users as $user)
    {
        if(!$user->student)
            continue;

        echo '<tr id="'.$user->student->id.'">';
        if($user->student->active)
            echo '<td><span class="glyphicon glyphicon-ok">&nbsp;</span></td>';
        else
            echo '<td><span class="glyphicon glyphicon-remove">&nbsp;</span></td>';

        echo '<td>'.$user->uvanetid.'</td>';
        echo '<td>'.link_to('mailto:'.$user->email,$user->name).'</td>';
        echo '<td>'.$user->student->programme.'</td>';
        echo '<td>'.($user->student->tutor ? link_to('mailto:'.$user->student->tutor->email,$user->student->tutor->name) : '&nbsp;').'</td>';
        echo '<td>'.($user->student->remark ? nl2br($user->student->remark) : "&nbsp;").'</td>';

        if($user->student->group)
            echo '<td>'.link_to('staff/groups#'.$user->student->group_id,$user->student->group->name).'</a></td>';
        else
            echo '<td>&nbsp;</td>';


        echo '<td>'.link_to('staff/student-modal/'.$user->student->id,'',['class'=>'glyphicon glyphicon-pencil','data-toggle'=>'modal','data-target'=>'#modal']);
        echo '</tr>';
    }
    echo '</tbody></table>';
}
else
    echo '<p>Er zijn nog geen studenten in de database aanwezig.</p>';
