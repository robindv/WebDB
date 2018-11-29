<?php

function current_course_id()
{
    return strstr($_SERVER["SERVER_NAME"], "webai") ? 2 : 1;
}

function current_course()
{
    return \App\Models\Course::find(current_course_id());
}