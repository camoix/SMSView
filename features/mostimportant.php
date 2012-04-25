<?php

class mostimportant
{

    function render( $data, $top = 5)
    {
        require_once("./features/utils.php");
        $temp           = 'temp/';
        $datastring     = $data['table.satisfaction.data'];
        $schoolname     = $data['schoolnaam'];
        //konqord JSON is false becuse escape character on '
        $datastring     = str_replace('\\\'', '\'', $datastring);
        $satisfaction_data  = json_decode($datastring)->{'importance'};

        $headerStyle = array(
            'b' => 'double',
            'font' => 'Century Gothic',
        );
        
        $tableStyle = array(
            'font' => 'Century Gothic',
            'cell_color' => 'E6E6E6',         
            
        );


        $mostimportant_docx = new CreateDocx();
        
        $most_important_table = array();

        $column=215;
        $most_important_table[0][0] = ''; 
        $size_col = array(20);
        foreach($satisfaction_data as $key => $reference){
            if (($key != 'peiling') && ($key != 'alle_scholen') ){continue;}
            usort($reference, "cmp_reference_importance");
            if ($key == 'peiling'){
                $headerStyle['text'] = 'Onze school';
            } elseif ($key == 'alle_scholen') {
                $headerStyle['text'] = 'Alle scholen';
            } else{
                $headerStyle['text'] = $key;
            }
            $text = $mostimportant_docx->addElement('addText', array($headerStyle));
            $most_important_table[0][$column] = $text; 
            $row=1;
            foreach($reference as $category){
                if ($row>$top) continue;
                $tableStyle['cell_color'] = ($row&1)?'E6E6E6':'FFFFFF';
                $tableStyle['text'] = $row;
                $text = $mostimportant_docx->addElement('addText', array($tableStyle));
                $most_important_table[$row][0] = $text; 
                $tableStyle['text'] = $category[1];
                $text = $mostimportant_docx->addElement('addText', array($tableStyle));
                $most_important_table[$row][$column] = $text ; 
                $row++;
            }
            $size_col[] = (9725-215)/count($satisfaction_data);
            $column++;
        }

        $paramsTable = array(
            'border' => 'none',
            'border_sz' => 20,
            'border_spacing' => 0,
            'border_color' => '000000',
            'jc' => 'left',
            'size_col' => $size_col
        );

        $mostimportant_docx->addTable($most_important_table, $paramsTable);

        $mostimportant_docx->createDocx($temp.'mostimportant');
        unset($mostimportant_docx);
        return $temp.'mostimportant.docx';
        
    }
    
    function process( $data, &$docx, $top = 5)
    {
        require_once("./features/utils.php");
        $datastring     = $data['table.satisfaction.data'];
        //konqord JSON is false becuse escape character on '
        $datastring     = str_replace('\\\'', '\'', $datastring);
        $satisfaction_data  = json_decode($datastring)->{'importance'};

/*        $headerStyle = array(
            'b' => 'double',
            'font' => 'Century Gothic',
        );
        
        $tableStyle = array(
            'font' => 'Century Gothic',
            'cell_color' => 'E6E6E6',         
            
        );

        $most_important_table = array();

        $column=215;
        $most_important_table[0][0] = ''; 
        $size_col = array(20);
        foreach($satisfaction_data as $key => $reference){
            usort($reference, "cmp_reference_importance");
            $headerStyle['text'] = $key;
            $text = $docx->addElement('addText', array($headerStyle));
            $most_important_table[0][$column] = $text; 
            $row=1;
            foreach($reference as $category){
                if ($row>$top) continue;
                $tableStyle['cell_color'] = ($row&1)?'E6E6E6':'FFFFFF';
                $tableStyle['text'] = $row;
                $text = $docx->addElement('addText', array($tableStyle));
                $most_important_table[$row][0] = $text; 
                $tableStyle['text'] = $category[1];
                $text = $docx->addElement('addText', array($tableStyle));
                $most_important_table[$row][$column] = $text ; 
                $row++;
            }
            $size_col[] = (9725-215)/count($satisfaction_data);
            $column++;
        }

        $paramsTable = array(
            'border' => 'none',
            'border_sz' => 20,
            'border_spacing' => 0,
            'border_color' => '000000',
            'jc' => 'left',
            'size_col' => $size_col
        );

//        $mostimportant_table = $docx->addTable($most_important_table, $paramsTable);
                $mostimportant_table = $docx->addElement('addTable', $most_important_table);
//            print $mostimportant_table->__toString();
var_dump($mostimportant_table->toXMLString());

        $docx -> addTemplateVariable('class:mostimportance', $mostimportant_table->__toString());
//        $docx -> addTemplateVariable('class:mostimportance', $mostimportant_table);
*/

        foreach($satisfaction_data as $key => $reference){
            usort($reference, "cmp_reference_importance");
            $row = 0;
            foreach($reference as $category){
                $mostimportant_data[$row][$key] = $category[1];
                $row++;
            }
        }
        for ($i = 0; $i <= 9; $i++) {
                $category_peiling = strtolower($mostimportant_data[$i]['peiling']);
                $category_alle_scholen = strtolower($mostimportant_data[$i]['alle_scholen']);
                $difference = ($category_peiling == $category_alle_scholen) ? 'Net als' : 'In tegenstelling tot';
                $docx -> addTemplateVariable("class:mostimportantProperties:category:$i:peiling", strval($category_peiling));
                $docx -> addTemplateVariable("class:mostimportantProperties:category:$i:allescholen", strval($category_peiling));
                $docx -> addTemplateVariable("class:mostimportantProperties:difference:$i", strval($difference));
        }

        return $docx;
        
    }

}

function cmp_reference_importance($a, $b)
{
    if ($a[2] == $b[2]) {
        return 0;
    }
    return ($a[2] > $b[2]) ? -1 : 1;
}