<?php
echo '<h1>Server</h1>';

$group = Auth::user()->student->group;

if(!$group || !$group->server)
{
    echo '<p>Er is geen server gevonden.</p>';
}
elseif(! $group->server->configured)
{
    echo '<p>De server van jouw groep is nog niet geconfigureerd. Maak je geen zorgen, dat maken we op tijd in orde.</p>';
}
else
{
    $server = $group->server;

    echo '<div class="container">';
    echo '<div class="row"><span class="col-sm-2" style="text-align:left;font-weight: bold;">Naam</span>';
    echo '<span class="col-sm-4">'.link_to('http://'.$server->hostname).'</span></div>';
    echo '<div class="row"><span class="col-sm-2" style="text-align:left;font-weight: bold;">IP-adres</span>';
    echo '<span class="col-sm-4">'.$server->ip_address.'</span></div>';

    if($server->provider->type == 'cloudstack') {
        echo '<div class="row"><span class="col-sm-2" style="text-align:left;font-weight: bold;">State</span>';
        echo '<span class="col-sm-4">' . $server->state;

        if ($server->state == 'Stopped')
            echo '&nbsp;(' . link_to('student/server-on/' . $server->id, 'Aanzetten') . ')';

        echo '</span></div>';
    }
    echo '</div>';

    $su = \App\Models\ServerUser::where('server_id',$server->id)->where('created',1)->where('user_id',Auth::id())->first();
    if($su)
    {
        echo '<h2>Inloggegevens</h2>';
        echo '<p>Met onderstaande gegevens kun je inloggen op SSH en MySQL.</p>';

        echo '<div class="container">';
        echo '<div class="row"><span class="col-sm-2" style="text-align:left;font-weight: bold;">Gebruikersnaam</span>';
        echo '<span class="col-sm-4">'.$su->username.'</span></div>';
        echo '<div class="row"><span class="col-sm-2" style="text-align:left;font-weight: bold;">Wachtwoord</span>';
        echo '<span class="col-sm-4">'.$su->password.'</span></div>';
        echo '<div class="row"><span class="col-sm-2" style="text-align:left;font-weight: bold;">SSH-commando</span>';
        echo '<span class="col-sm-4">ssh '.$su->username.'@'.$server->hostname.'</span></div>';
        echo '<div class="row"><span class="col-sm-2" style="text-align:left;font-weight: bold;">phpMyAdmin</span>';
        echo '<span class="col-sm-4">'.link_to('https://'.$server->hostname.'/phpmyadmin').'</span></div>';
        echo '</div>';
    }

    // echo '<h2>Domeinnamen</h2>';
    // echo '<p>Als je een eigen domeinnaam wilt toevoegen aan je server, dan kun je die hieronder toevoegen.
    //          Tevens zul je een DNS A-record moeten laten verwijzen naar bovenstaand extern IP-adres.
    //          Wil je SSL voor je eigen domeinnaam? Neem dan contact op met de serverbeheerder.</p>';
    //
    // echo '<script type="text/javascript">
    //         $(function() {
    //           $("table").tablesorter({
    //             theme: "default",
    //             widgets: ["zebra"],
    //             headers: { 1:{sorter:false},3:{sorter:false} },
    //           });
    //         });
    //     </script>';
    //
    // echo '<table>';
    // echo '<thead>';
    // echo '<tr><th width="20">#</th><th width="30">SSL</th><th>Domein</th><th width="20">&nbsp;</th></tr></thead><tbody>';
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
    //     }
    //     else
    //     {
    //         echo '<td>&nbsp;</td>';
    //         echo '<td>'.link_to('http://'.$domain->domain,$domain->domain).'</td>';
    //     }
    //
    //     if($domain->locked)
    //         echo '<td>&nbsp;</td>';
    //     else
    //         echo '<td>'.link_to('student/domain-delete-modal/'.$domain->id,'',['class'=>'glyphicon glyphicon-remove','data-toggle'=>'modal','data-target'=>'#modal']).'</td>';
    //
    //     echo '</tr>';
    // }
    //
    // echo '</tbody></table>';
    //
    // echo '<br />'.link_to('student/domain-add-modal','Domein toevoegen',['class'=>'btn btn-default','data-toggle'=>'modal','data-target'=>'#modal']);

    echo '<h2>Contact</h2><p>Problemen met je server? Neem contact op met '.link_to('mailto:'.$server->course->admin_email,$server->course->admin_email).'</p>';
}
