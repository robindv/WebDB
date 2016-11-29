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

echo '<div class="container"><span class="col-sm-2" style="text-align:left;font-weight: bold;">Naam</span>';
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

echo '<h2>Gebruikersaccounts</h2>';

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



// echo '<h2>Domeinnamen</h2>';
//
// echo '<table>';
// echo '<thead>';
// echo '<tr><th width="20">#</th><th width="30">SSL</th><th>Domein</th><th>Certificaat</th><th>Sleutel</th></tr></thead><tbody>';
//
// $i = 0;
// foreach($server->domains as $domain)
// {
//     echo '<tr id="'.$domain->id.'"><td>'.++$i.'</td>';
//
//     if($domain->ssl)
//     {
//         echo '<td><span class="glyphicon glyphicon-lock"></span></td>';
//         echo '<td>'.link_to('https://'.$domain->domain,$domain->domain).'</td>';
//         echo '<td>'.$domain->ssl_certificate.'</td>';
//         echo '<td>'.$domain->ssl_private_key.'</td>';
//     }
//     else
//     {
//         echo '<td>&nbsp;</td>';
//         echo '<td>'.link_to('http://'.$domain->domain,$domain->domain).'</td>';
//         echo '<td>&nbsp;</td>';
//         echo '<td>&nbsp;</td>';
//     }
//
//     echo '</tr>';
// }
//
// echo '</tbody></table>';
