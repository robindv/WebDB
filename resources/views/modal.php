<script>
    $(function() {

        bind_buttons();


        function bind_buttons(e)
        {
            $('#modal').on('hidden.bs.modal',function(e) {
                $('#modal').data('bs.modal', null);
            });

            $('#modalSubmit').on("click",submit_form);

            $("#modal_form").submit(function(event){
                event.preventDefault();
                submit_form();
            });

        }

        function submit_form() {

            /* Update CKEditor fields */
            if(typeof CKEDITOR !== 'undefined')
                for(var instanceName in CKEDITOR.instances){
                    CKEDITOR.instances[instanceName].updateElement();
                }


            $.ajax({type:"POST",
                url: $("#modal_form").attr("action"),
                data: $("#modal_form").serialize(),
                success: dialog_display_data,
                beforeSend: function() {
                    $('#loader').show();
                    $('#modal-footer').hide();
                    $('#modal-body').html("<br /><br /><br />");
                },
                complete: function(){
                    $('#loader').hide();
                }});
        }

        function dialog_display_data(data)
        {
            if (data.trim() =="")
            {

                $('#modal').modal('hide').data('bs.modal', null);
                var s = $(document).scrollTop();
                window.location.assign("?s="+s);
            }
            else{
                $('#modal-content').html(data);
                //  bind_buttons();
            }
        }
    });
</script>


<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel"><?php echo $title; ?></h4>
</div>
<div class="modal-body" id="modal-body">

    <?php

    echo $form;

    ?>


</div>
<div class="modal-footer" id="modal-footer">
    <?php
    if($submit_text === false)
        echo '<button type="button" class="btn btn-default" data-dismiss="modal">Sluiten</button>';
    else
        echo '
    <button type="button" class="btn btn-default" data-dismiss="modal">Annuleren</button>
    <button type="button" class="btn btn-'.$class.'" id="modalSubmit">'.$submit_text.'</button>';
    ?>
</div>