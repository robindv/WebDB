<h2>Servers</h2>

<script type="text/javascript">
    $(function() {
        $("table").tablesorter({
            theme: 'default',
            widgets: ["saveSort", "zebra"],
            headers: { 2:{sorter:false}, 7:{sorter:false} },
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
if($servers)
{
    echo '<button type="button" class="btn btn-default pull-right">Sorteren ongedaan maken</button>';
    echo '<table style="font-size:12px;" class="tablesorter">';
    echo '<thead><tr align="left">';
    echo '<th>Server</th><th>IP adres</th><th>Aangemaakt</th>';
    echo '<th>Geheugen</th><th>State</th><th>Up Time</th><th>Groep</th>';
    echo '<th>&nbsp;</th></tr></thead><tbody>';
    foreach($servers as $server)
    {
        echo '<tr id="'.$server->id.'">';

        echo '<td>'.link_to('staff/server/'.$server->id,$server->name).'</td>';
        echo '<td>'.$server->ip_address.'</td>';

        if($server->created)
            echo '<td><span class="glyphicon glyphicon-ok"></span>';
        else
            echo '<td><span class="glyphicon glyphicon-remove"></span>';

        if($server->configured)
            echo '<span class="glyphicon glyphicon-ok"></span></td>';
        else
            echo '<span class="glyphicon glyphicon-remove"></span></td>';

        if($server->memory)
            echo '<td>'.($server->memory/(1024*1024)).' MiB</td>';
        else
            echo '<td>&nbsp;</td>';

        if($server->state == 'Off')
            echo '<td>'.link_to('staff/server-on/'.$server->id,$server->state).'&nbsp;</td>';
        else
            echo '<td>'.$server->state.'&nbsp;</td>';
        echo '<td>'.$server->uptime.'&nbsp;</td>';


        if($server->group)
            echo '<td>'.link_to('staff/groups#'.$server->group->id,$server->group->name).'</td>';
        else
            echo '<td>&nbsp;</td>';

        if($server->state == 'Running')
            echo '<td><a href="http://'.$server->hostname.'.'.env('WEBDB_URL').'" class="glyphicon glyphicon-globe"></a></td>';
        else
            echo '<td>&nbsp;</td>';

        echo '</tr>';
    }
    echo '</tbody></table>';
}
else
    echo '<p>Er zijn nog geen servers in de database aanwezig.</p>';
