<?php

function template_paganverse_above()
{
global $txt, $modSettings;

if (!empty($modSettings['havamal']))
   {
      $num = rand(1,165);
      
      echo '<div class="paganverse"><strong>',$txt['hava'],'</strong><br />', $txt['paganverse' . $num], '</div>';
   }
   if (!empty($modSettings['witchlaw']))
   {
      $num = rand(166,199);
      
      echo '<div class="paganverse"><strong>',$txt['witch'],'</strong><br />', $txt['paganverse' . $num], '</div>';
   }
   if (!empty($modSettings['wiccanrede']))
   {
      $num = rand(200,222);
      
      echo '<div class="paganverse"><strong>',$txt['rede'],'</strong><br />', $txt['paganverse' . $num], '</div>';
   }
   if (!empty($modSettings['thelema']))
   {
      $num = rand(223,250);
      
      echo '<div class="paganverse"><strong>',$txt['thelema1'],'</strong><br />', $txt['paganverse' . $num], '</div>';
   }
   if (!empty($modSettings['paganverse']))
   {
      $num = rand(1,250);
      
      echo '<div class="paganverse"><strong>',$txt['pagantitle'],'</strong><br />', $txt['paganverse' . $num], '</div>';
   }
}
function template_paganverse_below()
{}
?>