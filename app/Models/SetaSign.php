<?php

namespace App\Models;

use Exception;
use Lang;
use SetaPDF_Core_Document;
use SetaPDF_Core_Image;
use SetaPDF_Core_Reader_File;
use SetaPDF_Core_Writer_File;
use SetaPDF_Core_XObject_Form;
use SetaPDF_FormFiller;
use SetaPDF_FormFiller_Field_Button;
use SetaPDF_FormFiller_Field_ButtonGroup;
use SetaPDF_FormFiller_Field_Combo;
use SetaPDF_FormFiller_Field_List;
use SetaPDF_Merger;

class SetaSign
{

    public static function mergePDFDocuments($files, $outputFilename, $page_number = false)
    {
        try {
            $writer = new SetaPDF_Core_Writer_File($outputFilename);
            $merger = new SetaPDF_Merger();

            foreach ($files as $file) {
                if (file_exists($file)) {
                    try {
                        $document = SetaPDF_Core_Document::loadByFilename($file);
                        $merger->addDocument($document);
                    } catch (Exception $e) {
                        //var_dump($e);
                        continue;
                    }
                }
            }
            $merger->setRenameSameNamedFormFields(false);
            // merge all files
            $merger->merge();

            // send the resulting document to the client
            $document = $merger->getDocument();
            $document->setWriter($writer);

            //Flatten all the fields in the document
            /*$formFiller = new SetaPDF_FormFiller($document);
            $fields = $formFiller->getFields();
            $fields->flatten();*/

            // create a stamper instance

            // create a font object
            if ($page_number) {
                $stamper = new SetaPDF_Stamper($document);
                $font = SetaPDF_Core_Font_Standard_Helvetica::create($document);
                $stamp = new SetaPDF_Stamper_Stamp_Text($font);
                $stamp->setBackgroundColor(1);

                // Callback to set the pagenumbers for each page
                function callbackForPageNumbering($pageNumber, $pageCount, $page, SetaPDF_Stamper_Stamp $stamp)
                {
                    $footer_text = "";
                    $textForPage = " Page";
                    $textForOf = "of";
                    //$footer_text = "\n Produced by WELD IT AS ®";
                    $stamp->setFontSize(10);

                    // set the text for the stamp object before stamping
                    $stamp->setText($textForPage . " $pageNumber " . $textForOf . " $pageCount" . $footer_text, 'UTF-8');

                    // if the callback don't return true the page won't be stamped
                    return true;
                }
                // create simple text stamp
                $stamp = new SetaPDF_Stamper_Stamp_Text($font);
                //$stamp->setPadding(4);
                //$stamp->setAlign(SetaPDF_Core_Text::ALIGN_CENTER);
                //$stamp->setText($stampText);

                $stamper->addStamp($stamp, array(
                    'position' => SetaPDF_Stamper::POSITION_RIGHT_BOTTOM,
                    'translateX' => -10,
                    'translateY' => 10,
                    'callback' => 'callbackForPageNumbering',
                ));
                // stamp the document
                $stamper->stamp();

            }
            // save the file and finish the writer (e.g. file handler will closed)
            $document->save()->finish();
        } catch (Exception $e) {
            echo "<pre>";
            echo ($e->xdebug_message);
            echo "</pre>";
        }
        return $outputFilename;

    }

    public static function generateTemplateReport($values, $source, $target, $signatures = array())
    {
        $count = 1;
        $new_values = array();
        $rows = array('AnsattRow', 'DatoRow', 'normalRow', '50Row', '100Row', 'DurationRow', 'CommentsRow');
        try {
            $reader = new SetaPDF_Core_Reader_File($source);
            $writer = new SetaPDF_Core_Writer_File($target);
            $document = SetaPDF_Core_Document::load($reader, $writer);

            $formFiller = new SetaPDF_FormFiller($document);

            // get the fields from the form filler
            $fields = $formFiller->getFields();

            $total_pages = 1;
            try {
                $total_rows = $fields->get('hourloggings_page_count')->getValue();
            } catch (Exception $e) {
                $total_rows = 16;//16 rows in the template file by default
            }
            if ($values['total_hourloggings'] > $total_rows) {
                $total_pages = intval($values['total_hourloggings'] / $total_rows);
                if (fmod($values['total_hourloggings'], $total_rows) > 0) {
                    $total_pages++;
                }
            }
            $values['pages'] = Lang::get('main.report.page') . $values['pages'] . " of " . $total_pages;
            foreach ($values as $key => $value) {
                try {
                    $field = $fields->get($key);
                    // cast the value to the correct type
                    if ($field instanceof SetaPDF_FormFiller_Field_Combo) {
                        $value = (int) $value;
                    } else if ($field instanceof SetaPDF_FormFiller_Field_Button) {
                        $value = (bool) $value;
                    } else if ($field instanceof SetaPDF_FormFiller_Field_ButtonGroup) {
                        $buttons = $field->getButtons();
                        $value = $buttons[(int) $value];
                    } else if ($field instanceof SetaPDF_FormFiller_Field_List) {
                        if (is_array($value)) {
                            $value = array_map('intval', $value);
                        } else if (!is_null($value)) {
                            $value = (int) $value;
                        }
                    }
                    $field->setValue($value);
                    if (strpos($key, "Row") !== false) {
                        unset($values[$key]);
                    }
                } catch (Exception $e){
                    $keys_exp = explode('Row', $key);
                    if($keys_exp && isset($keys_exp[1])){
                        foreach($rows as $row){
                            if(isset($values[$row . $keys_exp[1]])){
                                $new_values[$row . $count] = $values[$row . $keys_exp[1]];
                                unset($values[$row . $keys_exp[1]]);
                            }
                        }
                        $count++;
                    }       
                }
            }            
            //Add Signatures
            if (count($signatures)) {                
                foreach ($signatures as $key => $value) {
                    try{
                        if (file_exists($value)) {
                            $annotation = $fields[$key]->getAnnotation();
                            // Remember the width and height for further calculations
                            $width = $annotation->getWidth();
                            $height = $annotation->getHeight();

                            // Create a form xobject to which we are going to write the image.
                            // This form xobject will be the resulting appearance of our form field.
                            $xobject = SetaPDF_Core_XObject_Form::create($document, array(0, 0, $width, $height));
                            // Get the canvas for this xobject
                            $canvas = $xobject->getCanvas();

                            $image = SetaPDF_Core_Image::getByPath($value)->toXObject($document);
                            $imageWidth = 100;                    
                            // Draw the image onto the canvas with a width of 100 and align it to the middle of the height
                            $image->draw($canvas, 0, $height / 2 - $image->getHeight($imageWidth) / 2, $imageWidth);

                            // Now add the appearance to the annotation
                            $annotation->setAppearance($xobject);
                        }
                    }catch(Exception $e) {

                    }
                }                
            }

            $fields->flatten();
            $document->save()->finish();
        } catch (Exception $e) {
            //var_dump($e);
        }
        $values = array_merge($values, $new_values);        
        return array($values, $count - 1);
    }
}
