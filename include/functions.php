<?php

class HTML_helper
{
	
	function print_table($rows, $caption)
	{
		//take an array of arrays ($rows is an array of rows)
		//and print out a table to display each row.
		//use key fields as column headings
		//$caption is for table name
		$html = "
			<table class='table table-bordered table-hover table-condensed'>
				<caption class='text-left'>{$caption}</caption>
				<thead>
					<tr>";
		//Use first "row" to get column headings
		foreach($rows[0] as $key=>$value)
		{
			$html .= "<th>" . $key . "</th>";
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