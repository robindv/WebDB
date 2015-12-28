<h1>Profiel</h1>

<?php

echo '<div class="container">';
echo '<div class="row"><span class="col-sm-2" style="text-align:left;font-weight: bold;">Naam</span>';
echo '<span class="col-sm-4">'.$user->name.'</span></div>';
echo '<div class="row"><span class="col-sm-2" style="text-align:left;font-weight: bold;">UvAnetID</span>';
echo '<span class="col-sm-4">'.$user->uvanetid.'</span></div>';
echo '<div class="row"><span class="col-sm-2" style="text-align:left;font-weight: bold;">E-mailadres</span>';
echo '<span class="col-sm-4">'.$user->email.'</span></div>';

if($user->student)
{
    echo '<div class="row"><span class="col-sm-2" style="text-align:left;font-weight: bold;">Opleiding</span>';
    echo '<span class="col-sm-4">'.$user->student->programme.'</span></div>';
}

echo '</div>';
?>
<script type="text/javascript">
    $(function() {
        $("table").tablesorter({
            theme: 'default',
            widgets: ["zebra"],
        });
    });
</script>
<h2>Mijn wachtwoorden</h2>
<?php
if (!count($passwords))
{
    echo '<p>Er zijn geen wachtwoorden gevonden.</p>';
}
else
{
    echo '<table>';
    echo '<thead><tr><th>Server</th><th>SSH-poort</th><th>Gebruikersnaam</th><th>Wachtwoord</th></tr></thead><tbody>';
    foreach($passwords as $password)
    {
        echo '<tr>';
        echo '<td>'.$password->server->hostname.'.'.env('WEBDB_URL').'</td>';
        echo '<td>'.$password->server->ssh_port.'</td>';
        echo '<td>'.$password->username.'</td>';
        echo '<td>'.$password->password.'</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}
