<?php

class summary {

    function render($data, $ref) {

        $temp = 'temp/';
        $datastring = $data['top_questions_in_groups'];
        //konqord JSON is false becuse escape character on '
        $datastring = str_replace('\\\'', '\'', $datastring);
        $data = json_decode($datastring);
        //add graphic to docx
        $summary_docx = new CreateDocx();
//        $satisfactionPriorityScatter_docx->importStyles('./templates/otp-muis.docx', 'merge', array('Normal','ListParagraphPHPDOCX'));
        $summary_docx->importStyles('./templates/muis-style.docx', 'merge', array('Normal'));
        $data_array = array();
        foreach($data as $key => $category){
            $data_array[$key] = $category;
        }
        ksort($data_array);
        foreach($data_array as $key => $category){
            if ($key == "number_of_groups"){
                continue;
            }
            if ((count($category->top) > 0) or (count($category->bottom) > 0)){
                $summary_docx->addText($category->groupname,array(
                    'sz' => 10,
                    'color' => 'F78E1E',
                    'b' => 'double',
                    'font' => 'Century Gothic'
                ));
                $text = '';
                foreach($category->top as $topkey => $top){
                    if ($topkey == 0){
                        $text .= "Onze school wordt door relatief veel ouders gewaardeerd ten aanzien van '".filter_text($top[1])."' (".round($top[2]*100)."% van de ouders is hierover tevreden).";
                    }
                    if ($topkey == 1){
                        $text .= "Ook zijn relatief veel ouders tevreden over '".filter_text($top[1])."' (".round($top[2]*100)."%).";
                    }
                    
                }
                if ($text!= '') {
                    $summary_docx->addText($text,array(
                            'sz' => 10,
                            'font' => 'Century Gothic'
                    ));
                }
                $text = '';
                foreach($category->bottom as $bottomkey => $bottom){
                    if ($bottomkey == 0){
                        $text .= "Relatief veel ouders zijn ontevreden ten aanzien van '".filter_text($bottom[1])."' (".round($bottom[2]*100)."%)";
                    }
                    if ($bottomkey == 1){
                        $text .= "en '".filter_text($bottom[1],null, 'UTF-8')."' (".round($bottom[2]*100)."%)";
                    }
                }
                if ($text!= '') {
                    $text .= '.';
                    $summary_docx->addText($text,array(
                            'sz' => 10,
                            'font' => 'Century Gothic'
                    ));
                }
                
                $summary_docx->addBreak('line');
            }     
            
            
        }
 
		$filename = $temp . 'summary'.randchars(12);
        $summary_docx -> createDocx($filename);
        unset($summary_docx);
        return $filename . '.docx';

    }

    function process( &$data, &$docx)
    {
        require_once("./features/utils.php");
        $temp           = 'temp/';
        $datastring     = $data['count_peiling_forms'];
        //konqord JSON is false becuse escape character on '
        //konqord JSON is false becuse escape character on '
        $datastring     = str_replace('\\\'', '\'', $datastring);
        $all_questions  = json_decode($datastring);


        return $docx;
        
    }

    function _error_dump($object){
        ob_start();
        //var_dump($object);
        $contents = ob_get_contents();
        ob_end_clean();
        error_log($contents);
    }

}

