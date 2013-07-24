<?php

class HTML_helper
{
	function print_table($rows, $caption)
	{
		//Takes a 2d array ($rows is an array of rows) and prints out a table
		// to display each row using keys as column headings.
		// $caption is for table name
		$html = "
			<table class='table table-bordered table-hover table-condensed'>
				<caption class='text-left'>{$caption}</caption>
				<thead>
					<tr>";
		//Use first "row" to get column headings
		foreach($rows[0] as $key=>$value)
		{
			//NOTE: REMOVE following if, in order to dipslay ID number!
			if ($key != "id")
				$html .= "<th>" . ucfirst($key) . "</th>";
		}
		$html .= "
					</tr>
				</thead>
				<tbody>
			";
		foreach($rows as $row)
		{
			$html .= "<tr>";
			foreach($row as $key=>$value)
			{
				//NOTE: REMOVE following if, in order to dipslay ID number!
				if ($key != "id")
					$html .= "<td>{$row[$key]}</td>";
			}
			$html .= "</tr>";
		}
		$html .= "
				</tbody>
			</table>
		";
		return $html;
	}
}

?>