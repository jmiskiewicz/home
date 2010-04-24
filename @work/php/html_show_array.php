<?php  //you say html_show_array($array); to do it
function do_offset($level){
    $offset = "";             // offset for subarry
    for ($i=1; $i<$level;$i++){
    $offset = $offset . "<td></td>";
    }
    return $offset;
}

function show_array($array, $level, $sub){
    if (is_array($array) == 1){          // check if input is an array
       foreach($array as $key_val => $value) {
           $offset = "";
           if (is_array($value) == 1){   // array is multidimensional
           echo "<tr>";
           $offset = do_offset($level);
           echo $offset . "<td>" . $key_val . "</td>";
           show_array($value, $level+1, 1);
           }
           else{                        // (sub)array is not multidim
           if ($sub != 1){          // first entry for subarray
               echo "<tr nosub>";
               $offset = do_offset($level);
           }
           $sub = 0;
           echo $offset . "<td main ".$sub." width=\"280\">" . $key_val .
               "</td><td width=\"280\">" . $value . "</td>";
           echo "</tr>\n";
           }
       } //foreach $array
    } 
    else{ // argument $array is not an array
		echo "argument '$array' is not an array";
        return;
    }
}

function html_show_array($array){
  echo "<table cellspacing=\"0\" border=\"2\">\n";
  show_array($array, 1, 0);
  echo "</table>\n";
}

function sql_to_table($sql) {
		$fields_array=array();
        $num_fields=0;
        $num_row=0;
		
		// find position of "FROM" in query
        $fpos=strpos($sql, 'from');

        // get string starting from the first word after "FROM"
        $strfrom=substr($sql, $fpos+5, 50);

        // Find position of the first space after the first word in the string
        $Opos=strpos($strfrom,' ');

        //Get table name. If query pull data from more then one table only first table name will be read.
        $table=substr($strfrom, 0,$Opos);

           // Get result from query
            $result=mysql_query($sql) or die('Invalid query: ' . mysql_error());

            $num_row=mysql_numrows($result);

            print('<html>');
            print('<head><title>');
            print('View&nbsp'.$table.'</title>');
            print('<link rel="stylesheet" href="style.css">');

            print("</head>");
            print('<body><br>');

            if($num_row >0)
            {
                    //Get number of fields in query
                    $num_fields=mysql_num_fields($result);

         

           # get column metadata
            $i = 0;

             //Set table width 15% for each column
            $width=15 * $num_fields;

            print('<br><table width='.$width.'% align="center"><tr>');
            print('<tr><th colspan='.$num_fields.'>View&nbsp;'.$table.'</th></tr>');

             while ($i < $num_fields)
             {

              //Get fields (columns) names
            $meta = mysql_fetch_field($result);

            $fields_array[]=$meta->name;

           //Display column headers in upper case
       print('<th><b>'.strtoupper($fields_array[$i]).'</b></th>');

                    $i=$i+1;
                    }

            print('</tr>');
                

                   //Get values for each row and column
                while($row=mysql_fetch_row($result))
                {
                 print('<tr>');

                        for($i=0; $i<$num_fields; $i++)
                        {
                        //Display values for each row and column
                        print('<td>'.$row[$i].'</td>');

                        }

                print('</tr>');
                }

    }
	return;
	}

	function mssql_to_table($sql) {
		$fields_array=array();
        $num_fields=0;
        $num_row=0;
		
		// find position of "FROM" in query
        $fpos=strpos($sql, 'from');

        // get string starting from the first word after "FROM"
        $strfrom=substr($sql, $fpos+5, 50);

        // Find position of the first space after the first word in the string
        $Opos=strpos($strfrom,' ');

        //Get table name. If query pull data from more then one table only first table name will be read.
        $table=substr($strfrom, 0,$Opos);

           // Get result from query
            $result=mssql_query($sql) or die('Invalid query: ' . mssql_error());

            $num_row=mssql_numrows($result);

            print('<html>');
            print('<head><title>');
            print('View&nbsp'.$table.'</title>');
            print('<link rel="stylesheet" href="style.css">');

            print("</head>");
            print('<body><br>');

            if($num_row >0)
            {
                    //Get number of fields in query
                    $num_fields=mssql_num_fields($result);

         

           # get column metadata
            $i = 0;

             //Set table width 15% for each column
            $width=15 * $num_fields;

            print('<br><table width='.$width.'% align="center"><tr>');
            print('<tr><th colspan='.$num_fields.'>View&nbsp;'.$table.'</th></tr>');

             while ($i < $num_fields)
             {

              //Get fields (columns) names
            $meta = mssql_fetch_field($result);

            $fields_array[]=$meta->name;

           //Display column headers in upper case
       print('<th><b>'.strtoupper($fields_array[$i]).'</b></th>');

                    $i=$i+1;
                    }

            print('</tr>');
                

                   //Get values for each row and column
                while($row=mssql_fetch_row($result))
                {
                 print('<tr>');

                        for($i=0; $i<$num_fields; $i++)
                        {
                        //Display values for each row and column
                        print('<td>'.$row[$i].'</td>');

                        }

                print('</tr>');
                }

    }
	return;
	}
	
	
	
	
	
	
function createlist($default,$query,$blank)
	{
		if($blank)
		{
			print("<option select value=\"0\">$blank</option>");
		}

		$resultID = mysql_fetch_array($query);
		$num       = mysql_numrows($resultID); 
    
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_row($resultID,$i);
        
			if($row[0]==$default)$dtext = "selected";
				else $dtext = "";
    
			print("<option $dtext value=\"$row[0]\">$row[1]</option>");
		}
	}
	
	function generateSelect($name = '', $options = array()) 
	{
		$html = '<select name="'.$name.'">';
		foreach ($options as $option => $value) 
		{
			$html .= '<option value='.$value.'>'.$option.'</option>';
		}
		$html .= '</select>';
		return $html;
	}
?>
