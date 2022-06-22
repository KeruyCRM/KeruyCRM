<?php

header("Content-Type: application/javascript");

$sounds = [];
foreach (scandir('../sounds/') as $file) {
    if (substr($file, -4) == '.mp3') {
        $sounds[] = '
            {name: "' . substr($file, 0, -4) . '"}';
    }
}

$html = '
$(function () {
    
    ion.sound({
        sounds: [
            ' . implode(',', $sounds) . '        	
        ],
        path: "js/ion.sound-master/sounds/",
        preload: false,
        multiplay: false
    });
    
    /*
    $(window).click(function(){    
       ion.sound.play("chat");
    })
    */
})

function play_sond_by_id(id)
{
  if($("#"+id).val().length>0)
  {
     ion.sound.play($("#"+id).val());           
  }
}
';

echo $html;