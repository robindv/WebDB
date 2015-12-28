<?php

echo "Actief,UvAnetID,Voornaam,Achternaam,E-mailadres,Opleiding,Opmerking,Groep,Opmerkingen groep\n";
foreach($users as $user)
{
    if(!$user->student)
        continue;

    if($user->student->active)
        echo 'Ja';
    else
        echo 'Nee';

    echo ','.$user->uvanetid;
    echo ','.$user->firstname;

    if(empty($user->infix))
        echo ','.$user->lastname;
    else
        echo ','.$user->infix." ".$user->lastname;

    echo ','.$user->email;
    echo ',"'.$user->student->programme.'"';

    echo ',"'.($user->student->remark ? str_replace('"','""',$user->student->remark) : "").'"';

    if($user->student->group)
    {
        echo ','.$user->student->group->name;
        echo ',"'.($user->student->group->remark ? str_replace('"','""',$user->student->group->remark) : "").'"';
    }
    else
        echo ',,';



    echo "\n";
}