<?php
echo '<h1>Profiel</h1>';

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

echo '
<script type="text/javascript">
    $(function() {
        $("table").tablesorter({
            theme: "default",
            widgets: ["zebra"],
        });
    });
</script>';

echo '<h2>Mijn wachtwoorden</h2>';
if (!count($passwords))
{
    echo '<p>Er zijn geen wachtwoorden gevonden.</p>';
}
else
{
    echo '<table>';
    echo '<thead><tr><th>Server</th><th>Groep</th><th>Gebruikersnaam</th><th>Wachtwoord</th></tr></thead><tbody>';
    foreach($passwords as $password)
    {
        echo '<tr>';
        echo '<td>'.$password->server->hostname.'</td>';
        if($password->server->group)
            echo '<td>'.$password->server->group->name.'</td>';
        else
            echo '<td>-</td>';
        echo '<td>'.$password->username.'</td>';
        echo '<td>'.$password->password.'</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}
