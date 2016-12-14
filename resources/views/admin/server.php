<?php

echo '<h2>Server: '.$server->name.'</h2>';

echo '<script type="text/javascript">
        $(function() {
          $("table").tablesorter({
            theme: "default",
            widgets: ["zebra"],
            headers: { 1:{sorter:false},3:{sorter:false} },
          });
        });
    </script>';

$server->refresh();

if($server->created)
{
    echo '<div class="container"><span class="col-sm-2" style="text-align:left;font-weight: bold;">Hostname</span>';
    echo '<span class="col-sm-4">'.link_to('http://'.$server->hostname).'</span></div>';
    echo '<div class="container"><span class="col-sm-2" style="text-align:left;font-weight: bold;">IP-adres</span>';
    echo '<span class="col-sm-4">'.$server->ip_address.'</span></div>';
    echo '<div class="container"><span class="col-sm-2" style="text-align:left;font-weight: bold;">State</span>';
    echo '<span class="col-sm-4">'.$server->state;

    if($server->state == 'Stopped')
        echo '&nbsp;('.link_to('staff/server-on/'.$server->id,'Aanzetten').')';
    if($server->state == 'Running')
        echo '&nbsp;('.link_to('staff/server-off/'.$server->id,'Uitschakelen').')';

    echo '</span></div>';

    echo '<h2>SSL Certificaat</h2>';

    if($server->ssl_issuer == null)
        echo '<p>SSL certificaat nog niet aangemaakt.</p>';
    else
    {
        echo '<div class="container"><span class="col-sm-2" style="text-align:left;font-weight: bold;">Issuer</span>';
        echo '<span class="col-sm-4">'.$server->ssl_issuer.'</span></div>';
        echo '<div class="container"><span class="col-sm-2" style="text-align:left;font-weight: bold;">Geldig vanaf</span>';
        echo '<span class="col-sm-4">'.strftime("%d %b %Y %H:%M", $server->ssl_valid_from->timestamp).'</span></div>';
        echo '<div class="container"><span class="col-sm-2" style="text-align:left;font-weight: bold;">Geldig tot</span>';
        echo '<span class="col-sm-4">'.strftime("%d %b %Y %H:%M", $server->ssl_valid_to->timestamp).'</span></div>';
    }


}
else
    echo '<p>Deze server is nog niet aangemaakt.</p>';


echo '<h2>Gebruikersaccounts</h2>';

if($server->users->count() == 0)
{
    echo '<p>Er zijn nog geen gebuikers klaargezet voor deze server.</p>';
}
else
{
    echo '<table>';
    echo '<thead><tr><th>Naam</th><th>UvAnetID</th><th>Gebruikersnaam</th><th>Wachtwoord</th></tr></thead><tbody>';
    foreach($server->users as $su)
    {
        echo '<tr>';
        echo '<td>'.$su->user->name.'</td>';
        echo '<td>'.$su->user->uvanetid.'</td>';
        echo '<td>'.$su->username.'</td>';
        echo '<td>'.$su->password.'</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}

echo '<h2>Taken</h2>';
if($server->tasks->count() == 0)
{
    echo '<p>Er zijn nog geen taken aangemaakt voor deze server.</p>';
}
else
{
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Aangemaakt</th>';
    echo '<th>Taak</th>';
    echo '<th>Status</th>';
    echo '<th>Voltooid</th>';
    echo '</tr></thead><tbody>';

    foreach($server->tasks as $task)
    {
        echo '<tr><td>'.$task->created_at.'</td>';
        echo '<td>'.\App\Models\ServerTask::$actions[$task->action].'</td>';
        echo '<td>'.\App\Models\ServerTask::$states[$task->status].'</td>';
        echo '<td>'.$task->updated_at.'</td>';
        echo '</tr>';
    }
}
