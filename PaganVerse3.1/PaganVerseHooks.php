<?php


function addPaganVerseLayer()
{
	global $context, $modSettings;
	
	loadlanguage('PaganVerse');
	
	if (!isset($_REQUEST['xml']) && (!empty($modSettings['havamal']) || !empty($modSettings['witchlaw']) || !empty($modSettings['wiccanrede']) || !empty($modSettings['thelema']) || !empty($modSettings['paganverse'])))
	{
		loadTemplate('paganverse');
        $layers = $context['template_layers'];
        $context['template_layers'] = array();
        foreach ($layers as $layer)
        {
			$context['template_layers'][] = $layer;
			if ($layer == 'body')
					$context['template_layers'][] = 'paganverse';
        }
	}
	
	// Add this to the header for CSS goo
	$context['html_headers'] .= '
		<style type="text/css">
			/*Pagan Verse Mod by Runic*/
			.paganverse
			{
				width: 55%;
				margin: auto;
				font-size: 10pt;
				text-align: center;
				padding: 1px;
				border: 1px solid #000;
			}
		</style>';
}

function addPaganAdminPerms(&$config_vars)
{
	$config_vars = array_merge($config_vars, array(
			array('check', 'havamal'),
			array('check', 'witchlaw'),
			array('check', 'wiccanrede'),
			array('check', 'thelema'),
			array('check', 'paganverse'),
		)
	);
}