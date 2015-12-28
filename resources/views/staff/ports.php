<h2>Poorten</h2>
<script type="text/javascript">
    $(function() {
        $("table").tablesorter({
            theme: 'default',
            widgets: ["saveSort", "zebra"],
            headers: { 4:{sorter:false}, 3:{sorter:false} },
            widgetOptions : { filter_saveFilters : true   } });

        $('button').click(function(){
            $('table')
                .trigger('saveSortReset')
                .trigger("sortReset");
            return false;
        });
    });
</script>
<?php
if($ports)
{
    echo '<button type="button">Sorteren ongedaan maken</button>';
    echo '<table style="font-size:12px;" class="tablesorter table">';
    echo '<thead><tr align="left">';
    echo '<th>ListenPort</th><th>Address</th><th>ConnectPort</th>';
    echo '</tr></thead><tbody>';
    foreach($ports as $port)
    {
        echo '<tr>';

        echo '<td>'.$port->ListenPort.'</td>';
        echo '<td>'.$port->ConnectAddress.'</td>';
        echo '<td>'.$port->ConnectPort.'</td>';
     //   echo '<td>'.link_to('staff/port-remove-modal/'.$port->ListenPort,'',['class'=>'glyphicon glyphicon-remove']).'</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}
else
    echo '<p>Er zijn nog geen servers in de database aanwezig.</p>';
