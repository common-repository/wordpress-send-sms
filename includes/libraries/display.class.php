<?php

class sendsms_display {

	/*
	** Retourne un tableau de données
	** @return void
	*/
	function getTable($idTable, $titres, $lignesDeValeurs, $classes, $idLignes, $script = '')
	{
		$barreTitre = '';
		$corpsTable = '';
		for($i=0; $i<count($titres); $i++)
		{
			$barreTitre .= '<th class="' . $classes[$i] . '" scope="col">' . $titres[$i] . '</th>';
		}
		if($barreTitre != '')
		{
			$barreTitre = '<tr valign="top">' . $barreTitre . '</tr>';
		}
		for($numeroLigne=0; $numeroLigne<count($lignesDeValeurs); $numeroLigne++)
		{
			$ligneDeValeurs = $lignesDeValeurs[$numeroLigne];
			$corpsTable .= '<tr id="' . $idLignes[$numeroLigne] . '" valign="top" >';
			for($i=0; $i<count($ligneDeValeurs); $i++)
			{
				$corpsTable .= '
					<td class="' . $classes[$i] . ' ' . $ligneDeValeurs[$i]['class'] . '">' . $ligneDeValeurs[$i]['value'] . '</td>';
			}
			$corpsTable .= '</tr>';
		}
		$table = $script . '<table id="' . $idTable . '" cellspacing="0" class="widefat post fixed" >
				<thead>
						' . $barreTitre . '
				</thead>
				<tfoot>
						' . $barreTitre . '
				</tfoot>
				<tbody >'
				 . $corpsTable . 
				'
				</tbody>
			</table>';
		return $table;
	}
}

?>